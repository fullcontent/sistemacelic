<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\Empresa;
use App\Models\Servico;
use App\Models\Faturamento;
use Illuminate\Http\Request;
use App\Models\FaturamentoServico;




class FaturamentoController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
       $faturamentos = Faturamento::all();

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
            $s = $empresa->servicosFaturar->pluck('id');
            $s2 = $s2->merge($s);
        }

 


        $servicosFaturar = Servico::with('financeiro')
                            ->whereIn('id', $s2)
                            ->whereHas('finalizado',function($q) use ($start_date, $end_date){
                                return $q->whereBetween('created_at', [$start_date,$end_date]);
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
        $servicos = [];
        //Selecionar os servicos based on servico_id of request

            foreach($request->faturamento as $f)
            {
                $s = Servico::with('financeiro')->find($f['servico_id']);
                
                $valorFaturar = $f['valorFaturar'];

                $this->atualizarFinanceiro($s->id,$valorFaturar);
              
                array_push($servicos, $s->id);
            }

        //======================================================



        $servicosFaturar = Servico::with('financeiro')
                            ->whereIn('id',$servicos)
                            ->get();
              

        $total = $servicosFaturar->sum('financeiro.valorFaturar');

                
        $this->salvarFaturamento($servicos, $total, $request->obs, $request->descricao, $request->empresa_id);
                     

        return view('admin.faturamento.step4')->with([
            
            'faturamentoItens'=>$servicosFaturar,
            'totalFaturamento'=>$total,
            'descricao'=>$request->descricao,
            'obs'=>$request->obs,
        ]);

    }



    public function atualizarFinanceiro($servico_id, $valorFaturar)
    {
       
        $s = Servico::with('financeiro')->find($servico_id);

        
            
        if($s->financeiro->valorFaturado > 0)
        {
            
            if($valorFaturar+$s->financeiro->valorFaturado == $s->financeiro->valorTotal)
            {
                
                    //Para faturamento completo
                    $s->financeiro()->update([
                        'valorAberto'=>0,
                        'valorFaturado'=>$s->financeiro->valorTotal,
                        'valorFaturar'=>$valorFaturar,
                        'status'=>'faturado',
                    ]);
            }
            else{
                               
                $s->financeiro()->update([
                    'valorAberto'=>$s->financeiro->valorAberto - $valorFaturar,
                    'valorFaturado'=>$s->financeiro->valorFaturado + $valorFaturar,
                    'valorFaturar'=>$valorFaturar,
                    'status'=>'parcial',
                ]);


            }

            
        }

        else{

            //Se nao tiver nada faturado

            if($valorFaturar == $s->financeiro->valorTotal)
                {
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

                //Para faturamento parcial
                    $s->financeiro()->update([
                        'valorAberto'=>$s->financeiro->valorAberto - $valorFaturar,
                        'valorFaturado'=>$valorFaturar,
                        'valorFaturar'=>$valorFaturar,
                        'status'=>'parcial',
                    ]);

                }
            

        }

   
    }


    public function salvarFaturamento($servicos, $total, $obs, $descricao,$empresa_id)
    {
        
      
        $faturamento = new Faturamento;
        $faturamento->nome = $descricao;
        $faturamento->obs = $obs;
        $faturamento->valorTotal = $total;
        $faturamento->empresa_id = $empresa_id; //TEST

        $faturamento->save();
        
       
            foreach($servicos as $s)
            {   

                $servico = Servico::with('financeiro')->find($s);
                $faturamentoItem = new FaturamentoServico;
                $faturamentoItem->servico_id = $servico->id;
                $faturamentoItem->faturamento_id = $faturamento->id;
                $faturamentoItem->valorFaturado = $servico->financeiro->valorFaturado;
                $faturamentoItem->save();

                

            }

      
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

        //Selecionando faturamento a ser destruido

        $faturamento = Faturamento::find($id);

        
        //Selecionando os servicos dentro desse faturamento


        $faturamentoServicos = FaturamentoServico::where('faturamento_id',$id)->get();


        foreach($faturamentoServicos as $f)
        {
            $s = Servico::with('financeiro')->find($f->servico_id);
            $s->financeiro()->update([
                'valorAberto'=>$f->valorFaturado,
                'valorFaturado'=>$s->financeiro->valorTotal - $f->valorFaturado,
                'valorFaturar'=>0,
                'status'=>'aberto',
            ]);

            $f->destroy($f->id);
        }
        
        //Excluindo faturamento

        $faturamento->destroy($faturamento->id);


        return $this->index();

    }


    static function formatCnpjCpf($value)
    {
    $cnpj_cpf = preg_replace("/\D/", '', $value);
    
    if (strlen($cnpj_cpf) === 11) {
        return preg_replace("/(\d{3})(\d{3})(\d{3})(\d{2})/", "\$1.\$2.\$3-\$4", $cnpj_cpf);
    } 
    
    return preg_replace("/(\d{2})(\d{3})(\d{3})(\d{4})(\d{2})/", "\$1.\$2.\$3/\$4-\$5", $cnpj_cpf);
    }
    
}
