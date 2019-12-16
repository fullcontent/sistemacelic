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

    public function __construct()
    {
        
        $this->middleware('auth');

    }


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
        $validator = Validator::make($request->all(), [

                'responsavel'=>'required',
                'nomeFantasia' => 'required',
                'email' => 'required|email',

            ])->validate();

            $empresa = new Unidade;



            
            $empresa->empresa_id    = $request->empresa_id;
            $empresa->cnpj          = $request->cnpj;
            $empresa->nomeFantasia  = $request->nomeFantasia;
            $empresa->razaoSocial   = $request->razaoSocial;
            $empresa->status        = $request->status;
            $empresa->inscricaoEst  = $request->inscricaoEst;
            $empresa->inscricaoMun  = $request->inscricaoMun;
            $empresa->inscricaoImo  = $request->inscricaoImo;
            $empresa->codigo        = $request->codigo;
            $empresa->endereco      = $request->endereco;
            $empresa->numero        = $request->numero;
            $empresa->cep           = $request->cep;
            $empresa->complemento   = $request->complemento;
            $empresa->bairro        = $request->bairro;
            $empresa->cidade        = $request->cidade;
            $empresa->uf            = $request->uf;
            $empresa->telefone      = $request->telefone;
            $empresa->responsavel   = $request->responsavel;
            $empresa->email         = $request->email;
            $empresa->matriculaRI   = $request->matriculaRI;
            $empresa->area          = $request->area;
            $empresa->tipoImovel    = $request->tipoImovel;

            $empresa->save();

            return redirect()->route('unidades.show',$empresa->id)
                        ->with('success', 'A unidade '.$empresa->nomeFantasia.' foi criada com sucesso!');

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
        $empresas = Empresa::pluck('nomeFantasia','id')->toArray();
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


    public function editar(Request $request, $id)
    {
        

        $validator = Validator::make($request->all(), [

                'responsavel'=>'required',
                'nomeFantasia' => 'required',
                'email' => 'required|email',

        ])->validate();

        

        $empresa = Unidade::find($id);

            $empresa->empresa_id    = $request->empresa_id;
            $empresa->cnpj          = $request->cnpj;
            $empresa->nomeFantasia  = $request->nomeFantasia;
            $empresa->razaoSocial   = $request->razaoSocial;
            $empresa->status        = $request->status;
            $empresa->inscricaoEst  = $request->inscricaoEst;
            $empresa->inscricaoMun  = $request->inscricaoMun;
            $empresa->inscricaoImo  = $request->inscricaoImo;
            $empresa->codigo        = $request->codigo;
            $empresa->endereco      = $request->endereco;
            $empresa->numero        = $request->numero;
            $empresa->cep           = $request->cep;
            $empresa->complemento   = $request->complemento;
            $empresa->bairro        = $request->bairro;
            $empresa->cidade        = $request->cidade;
            $empresa->uf            = $request->uf;
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

    public function cadastro()
    {   
        $empresas = Empresa::pluck('nomeFantasia','id')->toArray();

        return view('admin.cadastro-unidade')->with('empresas',$empresas);
    }
}
