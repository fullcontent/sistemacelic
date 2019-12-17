<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\Servico;
use App\Models\Taxa;

class TaxasController extends Controller
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
        //
        $servicos = Servico::pluck('os','id')->toArray();

        return view('admin.cadastro-taxa')->with(['servicos'=>$servicos]);
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

        $taxa = new Taxa;

        $taxa->nome  = $request->nome;
        $taxa->emissao = date('Y-m-d',strtotime($request->emissao));
        $taxa->servico_id = $request->servico_id;
        $taxa->vencimento = date('Y-m-d',strtotime($request->vencimento));
        $taxa->valor =  str_replace (',', '.', str_replace ('.', '', $request->valor));
        $taxa->observacoes = $request->observacoes;
        $taxa->boleto   =   $request->boleto;
        $taxa->comprovante = $request->comprovante;
        $taxa->situacao = $request->situacao;

        $taxa->save();

        
        
        return redirect()->route('servicos.show',$request->servico_id);
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
