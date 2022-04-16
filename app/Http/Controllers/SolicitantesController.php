<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Solicitante;
use App\Models\Empresa;


class SolicitantesController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $solicitantes = Solicitante::all();

        return view('admin.solicitantes.lista')->with('solicitantes',$solicitantes);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $empresas = Empresa::orderBy('nomeFantasia')->pluck('nomeFantasia','id');



        return view('admin.solicitantes.cadastrar')->with('empresas',$empresas);
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

        $solicitante = new Solicitante;

        $solicitante->nome = $request->nome;
        $solicitante->email = $request->email;
        $solicitante->telefone = $request->telefone;
        $solicitante->departamento = $request->departamento;
        $solicitante->empresa_id = $request->empresa_id;

        $solicitante->save();

        // return $request->all();

        return redirect()->route('solicitantes.index');


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
        $solicitante = Solicitante::find($id);
        $empresas = Empresa::orderBy('nomeFantasia')->pluck('nomeFantasia','id');


        return view('admin.solicitantes.editar')->with(['empresas'=>$empresas,'solicitante'=>$solicitante]);
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
        $solicitante = Solicitante::find($id);

        $solicitante->nome = $request->nome;
        $solicitante->email = $request->email;
        $solicitante->telefone = $request->telefone;
        $solicitante->departamento = $request->departamento;
        $solicitante->empresa_id = $request->empresa_id;

        $solicitante->save();

        // return $request->all();

        return redirect()->route('solicitantes.index');

    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {   
        
        $solicitante = Solicitante::find($id);
        $solicitante->destroy($id);

        return redirect()->route('solicitantes.index');
    }
    
}
