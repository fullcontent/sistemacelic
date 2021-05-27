<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\Taxa;
use App\Models\Empresa;
use App\Models\Servico;
use App\Models\Reembolso;
use Illuminate\Http\Request;
use App\Models\ReembolsoTaxa;


use PDFMerger;

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
        

        $reembolsos = Reembolso::query()
       ->select('id','valorTotal','created_at','nome','empresa_id')
         ->with(['empresa' => function($query) {
            $query->select('id','nomeFantasia');
        }])
        ->get();

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
      
        $empresas = Empresa::all()->pluck('nomeFantasia','id');

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


        


        $servicosFaturar = Servico::whereIn('id', $s2)
                           
                            
                            ->with('reembolsos')
                            ->whereHas('reembolsos',function($q) use ($start_date, $end_date){
                                return $q->whereBetween('pagamento', [$start_date,$end_date]);
                            })                     
                            ->get();
        

        

        
        foreach($servicosFaturar as $s)
        {

            foreach($s->reembolsos as $r)
            {

                $t = $r->id;
                $taxas = $taxas->merge($t);
            }


        }

        


        $t = Taxa::whereIn('id',$taxas)->whereDoesntHave('reembolsada')->whereNotNull('pagamento')->get();
          

        return view('admin.reembolso.step2')->with([
            'taxas'=>$t,
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
        
        return view('admin.reembolso.step3')->with([
            'taxasReembolsar'=>$taxasReembolsar,
            'total'=>$total,
            'empresa'=> $empresa,
            'descricao'=>$descricao,

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


       return view('admin.reembolso.step4')->with([
            
            'reembolsoItens'=>$taxasReembolsar,
            'empresa'=>$empresa,
            'data'=>$data,
            'totalReembolso'=>$total,
            'descricao'=>$request->descricao,
            'obs'=>$request->obs,
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

        
       


        // $reembolsoR = \PDF::loadHTML('<h1>Test</h1>');

        $reembolsoR = \PDF::loadview('admin.reembolso.pdf',[
            'empresa'=>$empresa,
            'reembolsoItens'=>$reembolso->taxas,
            'descricao'=>$reembolso->nome,
            'obs'=>$reembolso->obs,
            'data'=>$reembolso->created_at,
            'totalReembolso'=>$reembolso->valorTotal,
            ]);

              
        

        $reembolsoR->save(public_path('uploads/ReembolsoTemp.pdf'));

        $reembolso = Reembolso::with('taxas')->find($id);
        $pdf = new PDFMerger();


        $pdf->addPDF(public_path('uploads/ReembolsoTemp.pdf'),'all');


        


       foreach($reembolso->taxas as $t)
       {

            $taxa = Taxa::find($t->taxa_id);
            $pdf->addPDF(public_path("uploads/".$taxa->comprovante), 'all');
            $pdf->addPDF(public_path("uploads/".$taxa->boleto), 'all');
            
       }


           
      // Merge the files and retrieve its PDF binary content
      $pdf->merge('download', "".utf8_encode($reembolso->empresa->nomeFantasia)." Relatorio Reembolso ".$reembolso->nome."");
       
      

    }
}
