<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Empresa;
use App\Models\Servico;




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
        
        $empresa = Empresa::find($request->empresa_id);
        $l = Empresa::with('servicosFaturar')->find($request->empresa_id);
        
        $servicosFaturar = $l->servicosFaturar;


        // dd($servicosFaturar);


        return view('admin.faturamento.step2')->with([
                'servicosFaturar'=>$servicosFaturar,
                'empresa'=>$empresa,
            ]);

    }

    public function step3(Request $request)
    {
        

        $servicosFaturar = Servico::whereIn('id',$request->servicos_id)->get();
        return $servicosFaturar;


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
