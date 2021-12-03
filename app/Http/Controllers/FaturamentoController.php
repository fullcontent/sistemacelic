<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\Empresa;
use App\Models\Servico;
use App\Models\Faturamento;
use Illuminate\Http\Request;
use App\Models\FaturamentoServico;
use App\Models\ServicoFinanceiro;
use App\Models\ServicoFinalizado;
use DB;




class FaturamentoController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
       $faturamentos = Faturamento::query()
       ->select('id','valorTotal','created_at','nf','nome','empresa_id','obs')
         ->with(['empresa' => function($query) {
            $query->select('id','nomeFantasia');
        },'servicos' => function($query){
            $query->select('proposta');
        }])
        ->get();


    //    return $faturamentos;
    //    dd($faturamentos);
    // $faturamentos = Faturamento::where('empresa_id',16)->get();

        return view('admin.faturamento.lista-faturamentos')->with([
            'faturamentos'=>$faturamentos,
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

        return view('admin.faturamento.step1')->with(compact('empresas',$empresas));

    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */

    public function step2(Request $request)
    {
        
        $periodo = explode(' - ', $request->periodo);
        $start_date = Carbon::parse($periodo[0])->toDateTimeString();
        $end_date = Carbon::parse($periodo[1])->toDateTimeString();

        
        $empresas = Empresa::whereIn('id',$request->empresa_id)->get();
        $s = array();
        $s2 = collect();
        
        foreach($empresas->pluck('id') as $e)
        {

            $empresa = Empresa::whereHas('servicosFaturar')->with('servicosFaturar')->find($e);

            if($empresa)
            {
                $s = $empresa->servicosFaturar->pluck('id');
                $s2 = $s2->merge($s);
            }
            
            
        }


        
        $servicosFaturar = Servico::with('financeiro')
                            ->orWhereIn('id', $s2)
                            ->whereHas('servicoFinalizado', function($q) use ($start_date, $end_date){
                                return $q->whereBetween('finalizado', [$start_date,$end_date]);
                            })
                                                                                      
                            ->get();
              
                            
        
        
        return view('admin.faturamento.step2')->with([
                'servicosFaturar'=>$servicosFaturar,
                'empresas'=>$empresas,
                'periodo'=>$periodo,
            ]);

    }
    

    public function step3(Request $request)
    {   

        
        
        $servicosFaturar = Servico::with('financeiro')
                            ->whereIn('id',$request->servicos)
                            ->get();

        $total = $servicosFaturar->sum('financeiro.valorAberto');

        

        $descricao = "00".Carbon::now()->month."-".Carbon::now()->year."";


        return view('admin.faturamento.step3')->with([
            'servicosFaturar'=>$servicosFaturar,
            'total'=>$total,
            'empresa_id'=> $request->empresa_id,
            'descricao'=>$descricao,

        ]);
    

    }

    public function step4(Request $request)
    {      


       
        //Criando faturamento

        $faturamento = new Faturamento;
        $faturamento->empresa_id = $request->empresa_id;
        $faturamento->save();


       //dump("Criou o faturamento ".$faturamento->id."");


        $servicos = [];
        
        //Selecionar os servicos based on servico_id of request

            foreach($request->faturamento as $f)
            {
                $s = Servico::with('financeiro')->find($f['servico_id']);
                
                $valorFaturar = $f['valorFaturar'];


                //dump("Atualizando Financeiro");
                $this->atualizarFinanceiro($s->id, $valorFaturar);
                
                
                //dump("Inserindo item ".$s->id." no faturamento".$faturamento->id."");
                $this->salvarItemFaturamento($s->id, $faturamento->id, $valorFaturar);
              
                array_push($servicos, $s->id);


            }

        //======================================================



        $servicosFaturar = Servico::with('financeiro')
                            ->whereIn('id',$servicos)
                            ->get();
              

        $total = $servicosFaturar->sum('financeiro.valorFaturar');

        //dump("Atualizando Faturamento");        
        
        // $this->salvarFaturamento($total, $request->obs, $request->descricao, $request->empresa_id);
        $this->atualizarFaturamento($faturamento->id, $total, $request->obs, $request->descricao);
        

        return view('admin.faturamento.step4')->with([
            
            'faturamentoItens'=>$servicosFaturar,
            'totalFaturamento'=>$total,
            'descricao'=>$request->descricao,
            'obs'=>$request->obs,
        ]);

    }


    public function faturarServicoSub(Request $request)
    {
        

                      
        $servico = Servico::whereIn('id',$request->servicos)->first();
        

        
        if($servico->servicoPrincipal != null)
        {
            // dump("esse servico é sub");
            $subServicos = Servico::where('servicoPrincipal',$servico->servicoPrincipal)
                                    ->orWhere('id',$servico->servicoPrincipal)
                                    ->pluck('id');

        }
        else{
            // dump("esse servico é principal");
            $subServicos = Servico::whereIn('id',$servico->subServicos->pluck('id'))
                                    ->pluck('id');
        }
        
        
        $servicosFaturar = Servico::with('financeiro')
                                    ->whereIn('id',$subServicos)
                                    ->orWhere('id',$servico->id)
                                    // ->whereHas('servicoFinalizado')
                                    ->get();       




                
        
        $empresas = Empresa::where('id',$request->empresa_id)->get();


        return $subServicos;
                
        
        return view('admin.faturamento.step2')->with([
                'servicosFaturar'=>$servicosFaturar,
                'empresas'=>$empresas,
                
            ]);

    }



    public function atualizarFinanceiro($servico_id, $valorFaturar)
    {
       
       


        $s = Servico::with('financeiro')->find($servico_id);

              
        
        if($s->financeiro->valorFaturado == 0)
            {

                //dump("Esse servico NAO TEM FATURAMENTO NENHUM");
               
                //Se nao tiver nada faturado

                if($valorFaturar == $s->financeiro->valorTotal)
                    {
                        //dump("Faturando COMPLETO e nao tem nada faturado ainda");
                        //Para faturamento completo
                        $s->financeiro()->update([
                            'valorAberto'=>0,
                            'valorFaturado'=>$valorFaturar,
                            'status'=>'faturado',
                            'valorFaturar'=>$valorFaturar,
                        ]);
                                            
                    }
                elseif($valorFaturar < $s->financeiro->valorTotal)
                    {


                        //dump("Faturando Parcialmente e nao tem nada faturado ainda");
                        //Para faturamento parcial
                        $s->financeiro()->update([
                            'valorAberto'=>$s->financeiro->valorAberto - $valorFaturar,
                            'valorFaturado'=>$valorFaturar,
                            'valorFaturar'=>$valorFaturar,
                            'status'=>'parcial',
                        ]);

                    }
                

            }
        
        
        
        
        if($s->financeiro->valorFaturado > 0)
            {
                
                //Se já existir algum faturamento

                //dump("Esse servico ja tem algum faturamento");

                if($valorFaturar+$s->financeiro->valorFaturado == $s->financeiro->valorTotal)
                {
                        //dump("Faturando COMPLETO e ja tem algo faturado");
                        //Para faturamento completo
                        $s->financeiro()->update([
                            'valorAberto'=>$s->financeiro->valorAberto - $valorFaturar,
                            'valorFaturado'=>$s->financeiro->valorFaturado + $valorFaturar,
                            'valorFaturar'=>$valorFaturar,
                            'status'=>'faturado',
                        ]);
                }
                else{
                        //Pra Faturamento parcial
                        //dump("Faturando Parcialmente e ja tem faturas");        
                        $s->financeiro()->update([
                            'valorAberto'=>$s->financeiro->valorAberto - $valorFaturar,
                            'valorFaturado'=>$s->financeiro->valorFaturado + $valorFaturar,
                            'valorFaturar'=>$valorFaturar,
                            'status'=>'parcial',
                        ]);


                }

                
            }

        

   
    }


    public function salvarFaturamento($total, $obs, $descricao,$empresa_id)
    {
        
      
        $faturamento = new Faturamento;
        $faturamento->nome = $descricao;
        $faturamento->obs = $obs;
        $faturamento->valorTotal = $total;
        $faturamento->empresa_id = $empresa_id; //TEST

        $faturamento->save();
        

             
    }


    public function atualizarFaturamento($faturamento_id, $total, $obs, $descricao)
    {
        $f = Faturamento::find($faturamento_id);

        
        $f->nome = $descricao;
        $f->obs = $obs;
        $f->valorTotal = $total;
        $f->save();
    }

    public function salvarItemFaturamento($servico, $faturamento_id, $valorFaturado)
    {
        

        $f2 = new FaturamentoServico;

        $f2->servico_id = $servico;
        $f2->faturamento_id = $faturamento_id;
        $f2->valorFaturado = $valorFaturado;
        $f2->save();


    }


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
        
        
        $faturamento = Faturamento::with('servicosFaturados.detalhes.unidade')->find($id);


        return view('admin.faturamento.detalhe-faturamento')->with([
            
            'faturamentoItens'=>$faturamento->servicosFaturados,
            'totalFaturamento'=>$faturamento->valorTotal,
            'descricao'=>$faturamento->nome,
            'obs'=>$faturamento->obs,
            'data'=>$faturamento->created_at,
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
    public function update(Request $request)
    {
        
        $faturamento = Faturamento::find($request->faturamentoID);

        $faturamento->nf=$request->nf;
        $faturamento->save();

        return redirect()->route('faturamentos.index');
        
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {

        //Selecionando faturamento a ser destruido

        $faturamento = Faturamento::find($id);

        
        //Selecionando os servicos dentro desse faturamento


        $faturamentoServicos = FaturamentoServico::where('faturamento_id',$id)->get();

        

        foreach($faturamentoServicos as $f)
        {
                   
            $s = Servico::with('financeiro')->find($f->servico_id);
            
            
            //Contar se há mais faturamentos para esse serviço;
            $c = FaturamentoServico::where('servico_id',$f->servico_id)->count();

            
            if($c > 1)
            {

                //dump("Servico faturado Parcialmente");

                
                if($s->financeiro->status == 'parcial')
                    {

                        //dump("Excluindo Servico faturado Parcialmente");

                        
                        
                        $s->financeiro()->update([

                            'valorAberto'=>$s->financeiro->valorAberto + $f->valorFaturado,
                            'valorFaturado'=>$s->financeiro->valorFaturado - $f->valorFaturado,
                            'valorFaturar'=>0,
                        ]);

                        
                        
                        $f->destroy($f->id);


                    }

                if($s->financeiro->status == 'faturado')
                    {

                        //dump("Excluindo Servico Faturado parcialmente mas já foi feito o valor total");

                        
                                                                   
                        $s->financeiro()->update([

                            'valorAberto'=>$s->financeiro->valorAberto + $f->valorFaturado,
                            'valorFaturado'=>$s->financeiro->valorFaturado - $f->valorFaturado,
                            'valorFaturar'=>0,
                            
                        ]);

                        
                        
                        $f->destroy($f->id);


                }

            }

            else{

                //dump("Excluindo Servico faturado Totalmente");
                               
                $s->financeiro()->update([

                    'valorAberto'=>$s->financeiro->valorAberto + $f->valorFaturado,
                    'valorFaturado'=>$s->financeiro->valorFaturado - $f->valorFaturado,
                    'valorFaturar'=>0,
                    'status'=>'aberto',
                ]);
    
                $f->destroy($f->id);

            }
            
            
        }
        
        //Excluindo faturamento

        // return "Excluindo Faturamento ".$faturamento->id."";

        $faturamento->destroy($faturamento->id);


        return $this->index();

    }

    public function addNF(Request $request)
    {
        
        $faturamento = Faturamento::find($request->faturamentoID);

        $faturamento->nf = $request->nf;
        $faturamento->save();

        return redirect()->route('faturamentos.index');

    }


    static function formatCnpjCpf($value)
    {
    $cnpj_cpf = preg_replace("/\D/", '', $value);
    
    if (strlen($cnpj_cpf) === 11) {
        return preg_replace("/(\d{3})(\d{3})(\d{3})(\d{2})/", "\$1.\$2.\$3-\$4", $cnpj_cpf);
    } 
    
    return preg_replace("/(\d{2})(\d{3})(\d{3})(\d{4})(\d{2})/", "\$1.\$2.\$3/\$4-\$5", $cnpj_cpf);
    }
    

    public function getAllServicesFinished()
    {
        
        $servicoFinalizado = Servico::whereHas('servicoFinalizado')->pluck('id');

        // dd($servicoFinalizado);


        
        
        $servicosFaturar = Servico::whereNotIn('id',$servicoFinalizado)->whereHas('finalizado')->get();


        // dd($servicosFaturar);

        foreach($servicosFaturar as $s)
        {
            
            
            
            if(!ServicoFinalizado::where('servico_id',$s->id)->first())
            {
                $servico = new ServicoFinalizado;
                $servico->servico_id = $s->id;
                $servico->finalizado = $s->finalizado->created_at;
                $servico->save();
   
                dump("Adicionado ".$s->id);
            }
            else{
                dump("JA FOI ".$s->id);
            }

            
           
        }

        
        


    }


    public function getErrors()
    {
        $servicos = ServicoFinanceiro::where('status','faturado')->get();

        foreach($servicos as $s)
        {

            if($s->valorAberto == $s->valorFaturado)
            {
                // $s->valorAberto = 0;
                // $s->valorFaturar = 0;
                // $s->save();
                
                dump($s->servico_id);
            }

        }

        
    }


}
