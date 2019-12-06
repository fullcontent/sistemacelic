<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Unidade;
use App\Models\Empresa;

use Illuminate\Support\Facades\Validator;




class UnidadesController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
        $unidades = Unidade::all();
        return view('admin.lista-unidades')->with('unidades',$unidades);
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
        $unidade = Unidade::find($id);

        return view('admin.detalhe-unidade')
                    ->with([
                        'dados'=>$unidade,
                        'servicos'=>$unidade->servicos,
                        'taxas'=>$unidade->taxas,
                        'route' => 'unidades.edit',
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
        $empresas = Empresa::select('id','nomeFantasia')->get();
        $unidade = Unidade::find($id);
        return view('admin.editar-unidade')
                    ->with([
                        'unidade'=>$unidade,
                        'empresas' => $empresas,
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
        

        


       $empresa = Empresa::find($id);





            $empresa->cnpj          = $request->cnpj;
            $empresa->nomeFantasia  = $request->nomeFantasia;
            $empresa->razaoSocial   = $request->razaoSocial;
            $empresa->inscricaoEst  = $request->inscricaoEst;
            $empresa->inscricaoMun  = $request->inscricaoMun;
            $empresa->inscricaoImo  = $request->inscricaoImo;
            $empresa->endereco      = $request->endereco;
            $empresa->numero        = $request->numero;
            $empresa->cep           = $request->cep;
            $empresa->complemento   = $request->complemento;
            $empresa->bairro        = $request->bairro;
            $empresa->telefone      = $request->telefone;
            $empresa->responsavel   = $request->responsavel;
            $empresa->email         = $request->email;
            $empresa->matriculaRI   = $request->matriculaRI;
            $empresa->area          = $request->area;
            $empresa->tipoImovel    = $request->tipoImovel;






            $empresa->save();

        return redirect()->route('unidades.index')->with('message','Editado com sucesso!');


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


    public function editar(Request $request, $id)
    {
        

        $validator = Validator::make($request->all(), [

                'responsavel'=>'required',
                'nomeFantasia' => 'required',
                'email' => 'required|email',

        ])->validate();

        

        $empresa = Unidade::find($id);

            $empresa->cnpj          = $request->cnpj;
            $empresa->nomeFantasia  = $request->nomeFantasia;
            $empresa->razaoSocial   = $request->razaoSocial;
            $empresa->inscricaoEst  = $request->inscricaoEst;
            $empresa->inscricaoMun  = $request->inscricaoMun;
            $empresa->inscricaoImo  = $request->inscricaoImo;
            $empresa->endereco      = $request->endereco;
            $empresa->numero        = $request->numero;
            $empresa->cep           = $request->cep;
            $empresa->complemento   = $request->complemento;
            $empresa->bairro        = $request->bairro;
            $empresa->telefone      = $request->telefone;
            $empresa->responsavel   = $request->responsavel;
            $empresa->email         = $request->email;
            $empresa->matriculaRI   = $request->matriculaRI;
            $empresa->area          = $request->area;
            $empresa->tipoImovel    = $request->tipoImovel;

            $empresa->save();
            

        return redirect()->route('unidades.show',$id)
                        ->with('success', 'A unidade '.$empresa->nomeFantasia.' foi editado com sucesso!');


    }
}
