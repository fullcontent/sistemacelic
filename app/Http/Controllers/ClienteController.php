<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Auth;


use App\UserAccess;
use App\User;
use App\Models\Empresa;
use App\Models\Unidade;

use App\Models\Servico;
use App\Models\Historico;
use Illuminate\Support\Facades\Validator;



class ClienteController extends Controller
{
    //
    public function __construct()
    {
        
        $this->middleware('auth');       

    }

    public function index()
    {   


    	return view ('cliente.dashboard')
                        ->with([
                            'servicos'=>$this->getServicosCliente(),

                        ]);
    }

    public function empresas()
    {
    	
        $user = User::find(Auth::id());

        $empresas = $user->empresas;

        return view('cliente.lista-empresas')->with('empresas',$empresas);

    }

    public function empresaShow($id)
    {
        $empresa = Empresa::find($id);
        return view('cliente.detalhe-empresa')
                    ->with([
                        'dados'=>$empresa,
                        'servicos'=>$empresa->servicos,
                        'taxas' => $empresa->taxas,
                        'route' => 'empresas.edit',
                    ]);
    }

    public function unidadeShow($id)
    {
        $unidade = Unidade::find($id);
        return view('cliente.detalhe-empresa')
                    ->with([
                        'dados'=>$unidade,
                        'servicos'=>$unidade->servicos,
                        'taxas' => $unidade->taxas,
                        'route' => 'unidades.edit',
                    ]);
    }

    public function empresaUnidades($id)
    {
        $unidades = Unidade::with('empresa')->where('empresa_id','=',$id)->get();
        return view('cliente.lista-unidades')->with('unidades',$unidades);
    }

    public function unidades()
    {
    	$user = User::find(Auth::id());

        $unidades = $user->unidades;

        return view('cliente.lista-unidades')->with('unidades',$unidades);
    }

    
    public function servicos()
    {   
        $user = User::find(Auth::id());

    	$servicos = Servico::whereIn('empresa_id',$user->empresas->pluck('id'))->orWhereIn('unidade_id', $user->unidades->pluck('id'))->get();

        return view('cliente.lista-servicos')
                    ->with('servicos', $servicos);


    }

    public function servicoShow($id)
    {
        $servico = Servico::find($id);

        if($servico->unidade_id){

            $dados = $servico->unidade;
            $route = 'unidades.edit';
        }
        else{
            $dados = $servico->empresa;
            $route = 'empresas.edit';
        }

        return view('cliente.detalhe-servico')
                    ->with([
                        'servico'=>$servico,
                        'dados'=>$dados,
                        'route'=>$route,
                    ]);
    }

    public function salvarInteracao(Request $request)
    {   

        $validator = Validator::make($request->all(), [

                'observacoes'=>'required',
                
            ])->validate();

        $interacao = new Historico;

        $interacao->servico_id = $request->servico_id;
        $interacao->observacoes = $request->observacoes;
        $interacao->user_id = Auth::id();

        $interacao->save();

        return redirect()->route('servico.show', $request->servico_id);
    }


    public function getServicosCliente()
    {
        $user = User::find(Auth::id());

        $servicos = Servico::whereIn('empresa_id',$user->empresas->pluck('id'))->orWhereIn('unidade_id', $user->unidades->pluck('id'))->get();

        return $servicos;
    }

    
}
