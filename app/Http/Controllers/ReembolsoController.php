<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\Taxa;
use App\Models\Empresa;
use App\Models\Servico;
use App\Models\Reembolso;
use Illuminate\Http\Request;
use App\Models\ReembolsoTaxa;
use App\Models\DadosCastro;


use PDFMerger;
use ZipArchive;
use File;

use Dompdf\Dompdf;
use PDF;



class ReembolsoController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        

    //     $reembolsos = Reembolso::query()
    //    ->select('id','valorTotal','created_at','nome','empresa_id')
    //      ->with(['empresa' => function($query) {
    //         $query->select('id','nomeFantasia');
    //     }])
    //     ->get();

        $reembolsos = Reembolso::all();

        return view('admin.reembolso.lista-reembolsos')->with([
            'reembolsos'=>$reembolsos,
        ]);

    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
      
        $empresas = Empresa::orderBy('nomeFantasia')->pluck('nomeFantasia','id');

        return view('admin.reembolso.step1')->with(compact('empresas',$empresas));
        
    }

    public function step2(Request $request)
    {
        
         
        
        $periodo = explode(' - ', $request->periodo);
        $start_date = Carbon::parse($periodo[0])->toDateTimeString();
        $end_date = Carbon::parse($periodo[1])->toDateTimeString();

        
        $empresas = Empresa::whereIn('id',$request->empresa_id)->get();
        $s = array();
        $s2 = collect();
        $taxas = collect();
        
        foreach($empresas->pluck('id') as $e)
        {

            $empresa = Empresa::find($e);
            $s = $empresa->servicosFaturar->pluck('id');
            $s2 = $s2->merge($s);
        }


       
        


        $servicosFaturar = Servico::with('reembolsos')
                            ->whereHas('reembolsos',function($q) use ($start_date, $end_date){
                                return $q->whereBetween('pagamento', [$start_date,$end_date]);
                            })                  
                            ->get();
        
        
        
                      
               
        foreach($servicosFaturar->whereIn('id',$s2) as $s)
        {

            foreach($s->reembolsos as $r)
            {
                
                $t = $r->id;
                $taxas = $taxas->merge($t);

            }


        }


        


        $taxasAberto = Taxa::whereIn('id',$taxas)
                ->whereDoesntHave('reembolsada')
                // ->whereHas('reembolsada')
                ->whereNotNull('pagamento')
                ->whereBetween('pagamento', [$start_date,$end_date])
                ->get();
                
                $taxasReembolsadas = Taxa::whereIn('id',$taxas)
                // ->whereDoesntHave('reembolsada')
                ->whereHas('reembolsada')
                ->whereNotNull('pagamento')
                ->whereBetween('pagamento', [$start_date,$end_date])
                ->get();    
                

       

        
        


        return view('admin.reembolso.step2')->with([
            'taxas'=>$taxasAberto,
            'reembolsadas'=>$taxasReembolsadas,
            'empresas'=>$empresas,
            'periodo'=>$periodo,
        ]);

        
        
    }


    public function step3(Request $request)
    {
        
        $taxasReembolsar = Taxa::whereIn('id', $request->taxas)->get();
        $total = $taxasReembolsar->sum('valor');
        $descricao = "00".Carbon::now()->month."-".Carbon::now()->year."";


        $empresa = Empresa::find($request->empresa_id);

        $dadosCastro = DadosCastro::pluck('razaoSocial','id');
        
        return view('admin.reembolso.step3')->with([
            'taxasReembolsar'=>$taxasReembolsar,
            'total'=>$total,
            'empresa'=> $empresa,
            'descricao'=>$descricao,
            'dadosCastro'=>$dadosCastro,

        ]);
    }

    public function step4(Request $request)
    {
      
        
        $taxasReembolsar = Taxa::whereIn('id',$request->taxas)->get();
        $total = $taxasReembolsar->sum('valor');
        $empresa = Empresa::find($request->empresa_id);

        setlocale (LC_TIME, 'pt-br');
        $data = \Carbon\Carbon::now()->formatLocalized('%d de %B de %Y');
        

        $this->salvarReembolso($taxasReembolsar, $total, $request->descricao, $request->obs, $request->empresa_id);

        $dadosCastro = DadosCastro::find($request->dadosCastro);

       
        

       return view('admin.reembolso.step4')->with([
            
            'reembolsoItens'=>$taxasReembolsar,
            'empresa'=>$empresa,
            'data'=>$data,
            'totalReembolso'=>$total,
            'descricao'=>$request->descricao,
            'obs'=>$request->obs,
            'dadosCastro' => $dadosCastro,
        ]);
        
    
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $reembolso = Reembolso::with('taxas.taxa.unidade')->find($id);

        $empresa = Empresa::find($reembolso->empresa_id);


        return view('admin.reembolso.detalhe-reembolso')->with([
            
            'reembolsoItens'=>$reembolso->taxas,
            'totalReembolso'=>$reembolso->valorTotal,
            'descricao'=>$reembolso->nome,
            'obs'=>$reembolso->obs,
            'data'=>$reembolso->created_at,
            'empresa'=>$empresa,
            'id'=>$this->fillWithZeros($reembolso->id),
            'dadosCastro'=>$reembolso->dadosCastro,
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
         //Selecionando reembolso a ser destruido

         $reembolso = Reembolso::find($id);

        
         //Selecionando os servicos dentro desse reembolso
 
 
         $reembolsoTaxas = ReembolsoTaxa::where('reembolso_id',$id)->get();
 
 
         foreach($reembolsoTaxas as $f)
         {
            $r = ReembolsoTaxa::find($f->id);
            $r->destroy($r->id);
         }
         
         //Excluindo reembolso
 
         $reembolso->destroy($reembolso->id);
 
 
         return $this->index();
    }

    public function salvarReembolso($taxas, $total, $descricao, $obs, $empresa_id)
    {
       
        $reembolso = new Reembolso;

        $reembolso->empresa_id = $empresa_id;
        $reembolso->nome = $descricao;
        $reembolso->obs = $obs;
        $reembolso->valorTotal = $total;

        $reembolso->save();

            foreach($taxas as $t)
            {

                $reembolsoTaxa = new ReembolsoTaxa;
                $reembolsoTaxa->taxa_id = $t->id;
                $reembolsoTaxa->reembolso_id = $reembolso->id;
                $reembolsoTaxa->save();

            }

    }

    
    public function download($id)
    {
        
        $reembolso = Reembolso::with('taxas.taxa.unidade')->find($id);
        $empresa = Empresa::find($reembolso->empresa_id);

      

        $id = $this->fillWithZeros($reembolso->id);
    

        $reembolsoR = \PDF::loadview('admin.reembolso.pdf',[
            'empresa'=>$empresa,
            'reembolsoItens'=>$reembolso->taxas,
            'descricao'=>$reembolso->nome,
            'obs'=>$reembolso->obs,
            'data'=>$reembolso->created_at,
            'totalReembolso'=>$reembolso->valorTotal,
            'id'=>$id,
            'dadosCastro'=>$reembolso->dadosCastro,
            ])->setPaper('a4', 'portrait');
            
        

        

        $reembolsoR->save(public_path('uploads/ReembolsoTemp.pdf'));

        return response()->file(public_path('uploads/ReembolsoTemp.pdf'));



        $reembolso = Reembolso::with('taxas')->find($id);

        $pdf = new PDFMerger();

        

        $pdf->addPDF(public_path('uploads/ReembolsoTemp.pdf'),'all');


        


       foreach($reembolso->taxas as $t)
       {

            $taxa = Taxa::find($t->taxa_id);

            if($taxa->comprovante)
            {
               
                $extension = pathinfo(public_path("uploads/".$taxa->comprovante), PATHINFO_EXTENSION);
                
                if($extension == "png" || $extension == "jpg" || $extension == "jpeg")
                {
                   $compPDF = \PDF::loadHTML("<img src=".public_path("uploads/".$taxa->comprovante." width=100%>"));
                   $compPDF->save(public_path('uploads/ComprovanteTEMP.pdf'));
                   $pdf->addPDF(public_path("uploads/ComprovanteTEMP.pdf"), 'all');
                }
                else
                {   
                    $fp = @fopen(public_path("uploads/".$taxa->comprovante), 'rb');
    
                            if (!$fp) {
                                return 0;
                            }
                            
                            /* Reset file pointer to the start */
                            fseek($fp, 0);
                            /* Read 20 bytes from the start of the PDF */
                            preg_match('/\d\.\d/',fread($fp,20),$match);
                            
                            fclose($fp);
                            
                            if (isset($match[0])) {

                                
                                if($match[0] <= 1.4)
                                {
                                    $pdf->addPDF(public_path("uploads/".$taxa->comprovante), 'all');
                                }
                               
                            }

                    
                }


            }

            if($taxa->boleto)
            {
                $extension = pathinfo(public_path("uploads/".$taxa->boleto), PATHINFO_EXTENSION);
                
                if($extension == "png" || $extension == "jpg" || $extension == "jpeg")
                {
                   $compPDF = \PDF::loadHTML("<img src=".public_path("uploads/".$taxa->boleto." width=100%>"));
                   $compPDF->save(public_path('uploads/boletoTEMP.pdf'));
                   $pdf->addPDF(public_path("uploads/boletoTEMP.pdf"), 'all');
                }
                else
                {

                    $fp = @fopen(public_path("uploads/".$taxa->boleto), 'rb');
    
                    if (!$fp) {
                        return 0;
                    }
                    
                    /* Reset file pointer to the start */
                    fseek($fp, 0);
                    /* Read 20 bytes from the start of the PDF */
                    preg_match('/\d\.\d/',fread($fp,20),$match);
                    
                    fclose($fp);
                    
                    if (isset($match[0])) {

                       
                        if($match[0] <= 1.4)
                        {
                            $pdf->addPDF(public_path("uploads/".$taxa->boleto), 'all');
                        }
                        
                        
                    }
                    
                    
                }
            }
           
            
            
       }

    
      // Merge the files and retrieve its PDF binary content
      $pdf->merge('browser', "".utf8_decode($reembolso->empresa->nomeFantasia)." Relatorio Reembolso ".$reembolso->nome.".pdf");
       
    }

    public function downloadZip($id)
    {   

        //check if exists

        $this->createRelatorioFolder($id);

        $rPath = public_path('uploads/reembolsos/'.$id);
        $reembolso = Reembolso::with('taxas.taxa.unidade')->find($id);

        $zip_file = $rPath."/".utf8_decode($this->tirarAcentos($reembolso->empresa->nomeFantasia))." - Relatorio Reembolso -".$reembolso->nome.".zip";

        
        $zip = new \ZipArchive();
        $zip->open($zip_file, \ZipArchive::CREATE | \ZipArchive::OVERWRITE);
        
        
        $files = new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator($rPath));
        
        
        


        foreach ($files as $name => $file)
        {   

            

            $extension = pathinfo($file->getRealPath(), PATHINFO_EXTENSION);

            if($extension != "zip")
            {   

                
                // We're skipping all subfolders
                if (!$file->isDir()) {

                    $filePath     = $file->getRealPath();

                    // extracting filename with substr/strlen
                    $relativePath = substr($filePath, strlen($rPath) + 1);

                    $zip->addFile($filePath, $relativePath);
                }
            }

        }

        
        $zip->close();

        return response()->download($zip_file);



    }


    public function createRelatorioFolder($id)
    {   

        $path = public_path('uploads/');
        $rPath = public_path('uploads/reembolsos/'.$id);

        //check if folder exists

        if($rPath)
        {

            $this->createFolder($id);
           
        }

        $reembolso = Reembolso::with('taxas.taxa.unidade')->find($id);
            $empresa = Empresa::find($reembolso->empresa_id);
            $reembolsoR = \PDF::loadview('admin.reembolso.pdf',[
                'empresa'=>$empresa,
                'reembolsoItens'=>$reembolso->taxas,
                'descricao'=>$reembolso->nome,
                'obs'=>$reembolso->obs,
                'data'=>$reembolso->created_at,
                'totalReembolso'=>$reembolso->valorTotal,
                'dadosCastro' => $reembolso->dadosCastro,
                ]);
        
        
        
            $reembolsoR->save($rPath.'/Reembolso - '.$reembolso->nome.'.pdf');

        

        foreach($reembolso->taxas as $key => $t)
        {
            $taxa = Taxa::find($t->taxa_id);
            $key = $key+1;
            if($taxa->comprovante)
            {   
                $extension = pathinfo(public_path("uploads/".$taxa->comprovante), PATHINFO_EXTENSION);
                $compr = File::copy($path.$taxa->comprovante, $path.'/reembolsos/'.$id.'/Item '.$key.' - Comprovante.'.$extension);
            }
            if($taxa->boleto)
            {   
                $extension = pathinfo(public_path("uploads/".$taxa->boleto), PATHINFO_EXTENSION);
                $compr = File::copy($path.$taxa->boleto, $path.'/reembolsos/'.$id.'/Item '.$key.' - Boleto.'.$extension);
            }

        }

        
                    
    }


    private function createFolder($id)
    {
        $path = public_path('uploads/reembolsos/'.$id);
        File::makeDirectory($path, $mode = 0777, true, true);
        

    }

    private function tirarAcentos($string){
        return preg_replace(array("/(á|à|ã|â|ä)/","/(Á|À|Ã|Â|Ä)/","/(é|è|ê|ë)/","/(É|È|Ê|Ë)/","/(í|ì|î|ï)/","/(Í|Ì|Î|Ï)/","/(ó|ò|õ|ô|ö)/","/(Ó|Ò|Õ|Ô|Ö)/","/(ú|ù|û|ü)/","/(Ú|Ù|Û|Ü)/","/(ñ)/","/(Ñ)/","/(Ç)/","/(ç)/","/(Ã)/"),explode(" ","a A e E i I o O u U n N C c A"),$string);
    }

    public function fillWithZeros($number) {
          // Se o número for menor que um limite (ex: 1000) adicionamos o offset
    // Para que 14 vire 1014
    
        return (string) ($number + 1000);
   
       
   
       }

    public function alterarEmpresa(Request $request)
    {
        return $request;
    }
       



    
}
