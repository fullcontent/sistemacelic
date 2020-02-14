<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Arquivo;
use Illuminate\Support\Facades\Storage;


class ArquivosController extends Controller
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

        $a = new Arquivo;

        if ($request->hasFile('arquivo') && $request->file('arquivo')->isValid()) {
                $nameFile = null;
                $name = uniqid(date('HisYmd'));
                $extension = $request->arquivo->extension();
                $nameFile = "{$name}.{$extension}";
                // Faz o upload:
                $upload = $request->arquivo->storeAs('arquivos', $nameFile);
                // Se tiver funcionado o arquivo foi armazenado em storage/app/public/categories/nomedinamicoarquivo.extensao

                $a->arquivo = $upload;

        }

        $a->nome = $request->nome;

        

        if($request->empresa_id)
        {
            $route = 'empresas.show';
            $id = $request->empresa_id;
            $a->empresa_id = $request->empresa_id;
        }

        if($request->unidade_id)
        {
            $route = 'unidades.show';
            $id = $request->unidade_id;
            $a->unidade_id = $request->unidade_id;
        }

        if($request->servico_id)
        {
            $route = 'servicos.show';
            $id = $request->servico_id;
            $a->servico_id = $request->servico_id;
        }

 
        $a->save();

        
        return redirect()->route($route,$id);

        
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

    public function download($id)
    {
        
        $file = Arquivo::find($id);

        $filename = $file->arquivo;
        $extension = substr($filename, -4);
        
        $arquivo = $file->unidade->codigo.' - '
                    .$file->unidade->nomeFantasia.' - '.$file->nome.$extension;

        
        
        return response()->download(public_path('uploads/'.$file->arquivo.''),$arquivo);

    }

    public function delete($id)
    {
        $file = Arquivo::find($id);

        $delete = Storage::delete($file->arquivo);

        $file->delete();



        return redirect()->back();
    }
}
