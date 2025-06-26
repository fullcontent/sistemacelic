<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Prestador;
use App\Models\PrestadorComentario;
use Auth;

class PrestadorController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $prestadores = Prestador::all();

        return view('admin.prestadores.lista-prestadores')->with('prestadores',$prestadores);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
        return view('admin.prestadores.cadastro-prestador');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {   


       
        
        $prestador = new Prestador();
        $prestador->nome = $request->nome;
        $prestador->cnpj = $request->cnpj;
        $prestador->qualificacao = $request->qualificacao;
        $prestador->telefone = $request->telefone;
        $prestador->email = $request->email;
        
       $prestador->ufAtuacao = $request->ufAtuacao;
       $prestador->cidadeAtuacao = json_encode($request->cidadeAtuacao);
       
        
        $prestador->chavePix = $request->input('chavePix');
        $prestador->tipoChave = $request->input('tipoChave');
        $prestador->banco = $request->input('banco');
        $prestador->agencia = $request->input('agencia');
        $prestador->conta = $request->input('conta');
        $prestador->formaPagamento = $request->input('formaPagamento');
        $prestador->tomadorNome = $request->input('tomadorNome');
        $prestador->tomadorCnpj = $request->input('tomadorCnpj');
        $prestador->cnpjVinculado = $request->input('cnpjVinculado');
        $prestador->razaoSocial = $request->input('razaoSocial');
        $prestador->obs = $request->input('obs');
    
        
        $prestador->save();
        // return $prestador;
        
        return redirect()->route('prestador.index')->with('success', 'Item created successfully.');

        

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
        return "prestador.show";
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        
        $prestador = Prestador::find($id);

        return view('admin.prestadores.editar-prestador')->with('prestador',$prestador);

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
    
        $prestador = Prestador::find($id);

        $prestador->nome = $request->nome;
        $prestador->cnpj = $request->cnpj;
        $prestador->qualificacao = $request->qualificacao;
        $prestador->telefone = $request->telefone;
        $prestador->email = $request->email;
        
        $prestador->cidadeAtuacao = json_encode($request->cidadeAtuacao);
        $prestador->ufAtuacao = $request->ufAtuacao;
       
        
        $prestador->chavePix = $request->input('chavePix');
        $prestador->tipoChave = $request->input('tipoChave');
        $prestador->banco = $request->input('banco');
        $prestador->agencia = $request->input('agencia');
        $prestador->conta = $request->input('conta');
        $prestador->formaPagamento = $request->input('formaPagamento');
        $prestador->tomadorNome = $request->input('tomadorNome');
        $prestador->tomadorCnpj = $request->input('tomadorCnpj');
        $prestador->cnpjVinculado = $request->input('cnpjVinculado');
        $prestador->razaoSocial = $request->input('razaoSocial');
        $prestador->obs = $request->input('obs');

        $prestador->save();

        



        return redirect()->route('prestador.index')->with('success', 'Item updated successfully.');


    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function delete($id)
    {
        $prestador = Prestador::destroy($id);
        return redirect()->route('prestador.index')->with('success', 'Prestador excluído com sucesso!');

    }


    public function rate(Request $request)
    {
       
       
       $prestador = Prestador::find($request->prestador_id);

       $prestadorComentario = new PrestadorComentario();
       $prestadorComentario->prestador_id = $request->prestador_id;
       $prestadorComentario->ordemCompra_id = $request->ordemCompra_id;
       $prestadorComentario->rating = $request->rating;
       $prestadorComentario->comentario = $request->comentario;
       $prestadorComentario->user_id = Auth::id();
       $prestadorComentario->save();

       return redirect()->route('ordemCompra.index')->with('success', 'Avaliacao feita!');
    }


    public function ratings(Request $request)
    {
        $data = PrestadorComentario::where('prestador_id',$request->prestador_id)->where('ordemCompra_id',$request->ordemCompra_id)->with('prestador','user','ordemCompra','ordemCompra.servicoPrincipal')->get();
        return response()->json($data);
    }
}
