<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Empresa;
use App\Models\Unidade;

use Auth;

use Illuminate\Support\Facades\Validator;

class EmpresasController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */

    public function __construct()
    {
        
        $this->middleware('admin');       

    }

    public function index()
    {
        //
        
        $empresas = Empresa::all();
        return view ('admin.lista-empresas')
            ->with([
                'empresas'=>$empresas,
            ]);
        

        
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
          
            $validator = Validator::make($request->all(), [

                'responsavel'=>'required',
                'nomeFantasia' => 'required',
                'email' => 'required|email',

            ])->validate();

            $empresa = new Empresa;

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

            return redirect()->route('empresas.show',$empresa->id)
                        ->with('success', 'A empresa '.$empresa->nomeFantasia.' foi criada com sucesso!');



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
         //
        
        $empresa = Empresa::find($id);

             
        return view('admin.detalhe-empresa')
                    ->with([
                        'dados'=>$empresa,
                        'servicos'=>$empresa->servicos,
                        'taxas' => $empresa->taxas,
                        'route' => 'empresas.edit',
                        'arquivo'=>'empresa',
                        
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
        $empresa = Empresa::find($id);
        return view('admin.editar-empresa')->with('empresa',$empresa);
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
    public function delete($id)
    {
       $unidade = Empresa::destroy($id);
    
        return $this->index();

    }

    public function unidades($id)
    {
        $unidades = Unidade::with('empresa')->where('empresa_id','=',$id)->get();
        return view('admin.lista-unidades')->with('unidades',$unidades);
    }

    public function editar(Request $request, $id)
    {
        

        $validator = Validator::make($request->all(), [

                'responsavel'=>'required',
                'nomeFantasia' => 'required',
                'email' => 'required|email',

        ])->validate();

        

        $empresa = Empresa::find($id);

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
            

            return redirect()->route('empresas.show',$empresa->id)
                        ->with('success', 'A empresa '.$empresa->nomeFantasia.' foi editado com sucesso!');


    }

    public function cadastro()
    {
        return view('admin.cadastro-empresa');
    }
}
