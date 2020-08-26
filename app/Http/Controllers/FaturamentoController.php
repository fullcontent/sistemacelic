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
        //
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

        $total = $servicosFaturar->sum('financeiro.valorFaturado');
       

        return view('admin.faturamento.step3')->with([
            'servicosFaturar'=>$servicosFaturar,
            'total'=>$total,

        ]);
    

    }

    public function step4(Request $request)
    {   
        $servicos = [];
        //Selecionar os servicos based on servico_id of request

            foreach($request->faturamento as $f)
            {
                $s = Servico::with('financeiro')->find($f['servico_id']);
                
                $newValorAberto = $s->financeiro->valorAberto - $f['valorFaturar'];
                $valorFaturado  = $f['valorFaturar'];

                // $financeiro = $s->financeiro()->update([
                //     'valorAberto'=>$newValorAberto,
                //     'valorFaturado'=>$valorFaturado,
                // ]);
              
                array_push($servicos, $s->id);
            }

        //======================================================



        $servicosFaturar = Servico::with('financeiro')
                            ->whereIn('id',$servicos)
                            ->get();
              

        $total = $servicosFaturar->sum('financeiro.valorFaturado');


        $this->salvarFaturamento($servicos, $total, $request->obs, $request->descricao);
                     

        return view('admin.faturamento.step4')->with([
            
            'faturamentoItens'=>$servicosFaturar,
            'totalFaturamento'=>$total,
            'descricao'=>$request->descricao,
            'obs'=>$request->obs,
        ]);

    }


    public function salvarFaturamento($servicos, $total, $obs, $descricao)
    {
        
        dump("Salvando Faturamento");

        $faturamento = new Faturamento;
        $faturamento->nome = $descricao;
        $faturamento->obs = $obs;
        $faturamento->valorTotal = $total;
        $faturamento->empresa_id = 16; //TEST

        $faturamento->save();
        
        dump("Faturamento Salvo");


        dump("Inserindo itens do faturamento");

            foreach($servicos as $s)
            {   

                $servico = Servico::with('financeiro')->find($s);
                $faturamentoItem = new FaturamentoServico;
                $faturamentoItem->servico_id = $servico->id;
                $faturamentoItem->faturamento_id = $faturamento->id;
                $faturamentoItem->valorFaturado = $servico->financeiro->valorFaturado;
                $faturamentoItem->save();

                dump("Inserindo iteM");    

            }

        dump("Finalizado");

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
        //
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
        //
    }
}
