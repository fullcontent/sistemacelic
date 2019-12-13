<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Servico;
use App\Models\Historico;


class ServicosController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        
        $servicos = Servico::all();
        return view('admin.lista-servicos')
                    ->with('servicos',$servicos);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {
        //

        $tipo = $request->tipo;
        $id = $request->id;




        return view('admin.cadastro-servico')->with(['tipo'=>$tipo,'id'=>$id]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        
        $servico = new Servico;
        $servico->tipo  =   $request->tipo;
        $servico->os    =   $request->os;
        $servico->nome  =   $request->nome;
        $servico->situacao  =  $request->situacao;
        $servico->protocolo_numero  =   $request->protocolo_numero;
        
        $servico->protocolo_emissao =   date('Y-m-d',strtotime($request->protocolo_emissao));
        $servico->protocolo_validade =  date('Y-m-d',strtotime($request->protocolo_validade));

        $servico->protocolo_anexo   = $request->protocolo_anexo;
        $servico->acao  =   $request->acao;
        $servico->pendencia = $request->pendencia;
        $servico->observacoes   = $request->observacoes;


        $servico->empresa_id = $request->empresa_id;
        $servico->unidade_id = $request->unidade_id;

        $servico->save();


        //Insert history

        $history = new Historico();
        $history->servico_id = $servico->id;
        $history->by = 'USER NAME';
        $history->observacoes = "Serviço cadastrado";
        $history->save();

        

        return redirect()->route('servicos.index');



        
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
        $servico = Servico::find($id);

        //Check if is empresa or unidade

        if($servico->unidade_id){

            $dados = $servico->unidade;
            $route = 'unidades.edit';
        }
        else{
            $dados = $servico->empresa;
            $route = 'empresas.edit';
        }


        return view('admin.detalhe-servico')
                    ->with([
                        'servico'=>$servico,
                        'dados'=>$dados,
                        'route'=>$route,
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
        

        $servico = Servico::find($id);

        //Check if is empresa or unidade

        if($servico->unidade_id){

            $dados = $servico->unidade;
            $route = 'unidades.edit';
        }
        else{
            $dados = $servico->empresa;
            $route = 'empresas.edit';
        }


        return view('admin.editar-servico')
                    ->with([
                        'servico'=>$servico,
                        'dados'=>$dados,
                        'route'=>$route,
                    ]);
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
        $servico = Servico::find($id);
        $servico->tipo  =   $request->tipo;
        $servico->os    =   $request->os;
        $servico->nome  =   $request->nome;
        $servico->situacao  =  $request->situacao;
        $servico->protocolo_numero  =   $request->protocolo_numero;
        
        $servico->protocolo_emissao =   date('Y-m-d',strtotime($request->protocolo_emissao));
        $servico->protocolo_validade =  date('Y-m-d',strtotime($request->protocolo_validade));

        $servico->protocolo_anexo   = $request->protocolo_anexo;
        $servico->acao  =   $request->acao;
        $servico->pendencia = $request->pendencia;
        $servico->observacoes   = $request->observacoes;


        $servico->save();


        //Insert history

        $history = new Historico();
        $history->servico_id = $servico->id;
        $history->by = 'USER NAME';
        $history->observacoes = "Serviço editado";
        $history->save();

        

        return redirect()->route('servicos.index');


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
