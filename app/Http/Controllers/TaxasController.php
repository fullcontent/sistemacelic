<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\Servico;
use App\Models\Taxa;
use App\Models\Historico;

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
        $taxa->emissao = Carbon::createFromFormat('d/m/Y', $request->emissao)->toDateString(); 
        $taxa->servico_id = $request->servico_id;
        $taxa->vencimento = Carbon::createFromFormat('d/m/Y', $request->vencimento)->toDateString(); 
        $taxa->valor =  str_replace (',', '.', str_replace ('.', '', $request->valor));
        $taxa->observacoes = $request->observacoes;
        // $taxa->boleto   =   $request->boleto;
        // $taxa->comprovante = $request->comprovante;
        $taxa->situacao = $request->situacao;

         // Se informou o arquivo, retorna um boolean
        if ($request->hasFile('boleto') && $request->file('boleto')->isValid()) {
                $nameFile = null;
                $name = uniqid(date('HisYmd'));
                $extension = $request->boleto->extension();
                $nameFile = "{$name}.{$extension}";
                // Faz o upload:
                $upload = $request->boleto->storeAs('boletos', $nameFile);
                // Se tiver funcionado o arquivo foi armazenado em storage/app/public/categories/nomedinamicoarquivo.extensao

                $taxa->boleto = $upload;

            }

         // Se informou o arquivo, retorna um boolean
        if ($request->hasFile('comprovante') && $request->file('comprovante')->isValid()) {
                $nameFile = null;
                $name = uniqid(date('HisYmd'));
                $extension = $request->comprovante->extension();
                $nameFile = "{$name}.{$extension}";
                // Faz o upload:
                $upload = $request->comprovante->storeAs('comprovantes', $nameFile);
                // Se tiver funcionado o arquivo foi armazenado em storage/app/public/categories/nomedinamicoarquivo.extensao

                $taxa->comprovante = $upload;


            }

        $taxa->save();


        //insert history

        $history = new Historico();
        $history->servico_id = $request->servico_id;
        $history->user_id = Auth::id();
        $history->observacoes = "Taxa ".$taxa->nome." cadastrada.";
        $history->save();

        
        
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
        $taxa = Taxa::find($id);
        $servicos = Servico::pluck('os','id')->toArray();

        return view('admin.editar-taxa')->with(['taxa'=>$taxa,'servicos'=>$servicos]);
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
        
        $taxa = Taxa::find($id);

        $taxa->nome  = $request->nome;
        $taxa->emissao = Carbon::createFromFormat('d/m/Y', $request->emissao)->toDateString(); 
        $taxa->servico_id = $request->servico_id;
        $taxa->vencimento = Carbon::createFromFormat('d/m/Y', $request->vencimento)->toDateString(); 
        $taxa->valor =  str_replace (',', '.', str_replace ('.', '', $request->valor));
        $taxa->observacoes = $request->observacoes;
        // $taxa->boleto   =   $request->boleto;
        // $taxa->comprovante = $request->comprovante;
        $taxa->situacao = $request->situacao;

         // Se informou o arquivo, retorna um boolean
        if ($request->hasFile('boleto') && $request->file('boleto')->isValid()) {
                $nameFile = null;
                $name = uniqid(date('HisYmd'));
                $extension = $request->boleto->extension();
                $nameFile = "{$name}.{$extension}";
                // Faz o upload:
                $upload = $request->boleto->storeAs('boletos', $nameFile);
                // Se tiver funcionado o arquivo foi armazenado em storage/app/public/categories/nomedinamicoarquivo.extensao

                $taxa->boleto = $upload;

            }

         // Se informou o arquivo, retorna um boolean
        if ($request->hasFile('comprovante') && $request->file('comprovante')->isValid()) {
                $nameFile = null;
                $name = uniqid(date('HisYmd'));
                $extension = $request->comprovante->extension();
                $nameFile = "{$name}.{$extension}";
                // Faz o upload:
                $upload = $request->comprovante->storeAs('comprovantes', $nameFile);
                // Se tiver funcionado o arquivo foi armazenado em storage/app/public/categories/nomedinamicoarquivo.extensao

                $taxa->comprovante = $upload;


            }

        $taxa->save();

        
        
        return redirect()->route('servicos.show',$request->servico_id);


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

    public function salvarHistorico()
    {
         //Insert history

        $history = new Historico();
        $history->servico_id = $servico->id;
        $history->user_id = Auth::id();
        $history->observacoes = "Taxa ".$servico->id." cadastrado.";
        $history->save();

    }
}
