<?php

namespace App\Http\Controllers;

use App\User;
use App\UserAccess;
use App\Models\Taxa;


use App\Models\Empresa;
use App\Models\Servico;
use App\Models\Unidade;
use App\Models\Historico;

use App\Models\Pendencia;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
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
                            'pendencias'=>$this->getPendenciasCliente(),


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
        $access = UserAccess::where('user_id',Auth::id())->whereNull('empresa_id')->get();

        if($access->pluck('unidade_id')->contains($id))
        {
            return view('cliente.detalhe-empresa')
                    ->with([
                        'dados'=>$unidade,
                        'servicos'=>$unidade->servicos,
                        'taxas' => $unidade->taxas,
                        'route' => 'unidades.edit',
                    ]);
        }
        else
        {
            return view('errors.403');
        }


        
        
    }

    public function empresaUnidades($id)
    {   
        $unidades = Unidade::with('empresa')->where('empresa_id','=',$id)->get();

        $access = UserAccess::where('user_id',Auth::id())->whereNull('unidade_id')->get();

        if($access->pluck('empresa_id')->contains($id))
        {
            return view('cliente.lista-unidades')->with('unidades',$unidades);
        }

        else
        {
            return view('errors.403');
        }




        
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

        $servicos = Servico::whereIn('empresa_id',$user->empresas->pluck('id'))
                            ->orWhereIn('unidade_id', $user->unidades->pluck('id'))
                            ->with('unidade','empresa','responsavel')
                            ->get();
        
        $servicos = $servicos->where('situacao','<>','arquivado');

        

        return view('cliente.lista-servicos')
                    ->with('servicos', $servicos)
                    ->with('title','Listando todos os serviços');


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
                        'taxas'=>$servico->taxas,
                        'pendencias'=>$servico->pendencias,
                    ]);
    }

    public function showTaxa(Request $request)
    {
        $taxa = Taxa::find($request->taxa);

        return view('cliente.detalhe-taxa')
                    ->with([
                        'taxa'=>$taxa,
                    ]);
    }


    public function listaAndamento()
    {
        
         $user = User::find(Auth::id());

        
        $servicos = Servico::with('unidade','empresa','responsavel')
                            ->whereIn('empresa_id',$user->empresas->pluck('id'))
                            ->orWhereIn('unidade_id', $user->unidades->pluck('id'))
                            ->where('situacao','andamento')
                            
                            ->get();


        $servicos = $servicos->where('situacao','=','andamento')
                              ->where('situacao','<>','arquivado');

    

        return view('cliente.lista-servicos')
                    ->with(
                        [
                            'servicos'=>$servicos,
                            'title'=>'Serviços em Andamento',
                        ]);
    }

    public function listaFinalizados()
    {
        
         $user = User::find(Auth::id());
        
        $servicos = Servico::with('unidade','empresa','responsavel')
                                ->whereIn('empresa_id',$user->empresas->pluck('id'))
                                ->orWhereIn('unidade_id', $user->unidades->pluck('id'))
                                ->get();

        $servicos = $servicos->where('situacao','=','finalizado')
                            ->where('situacao','<>','arquivado');                             

        return view('cliente.lista-servicos')
                    ->with(
                        [
                            'servicos'=>$servicos,
                            'title'=>'Serviços Finalizados',
                        ]);
    }

    public function listaVigentes()
    {   

         $user = User::find(Auth::id());
        
        $servicos = Servico::with('unidade','empresa','responsavel')
                        ->whereIn('empresa_id',$user->empresas->pluck('id'))
                        ->orWhereIn('unidade_id', $user->unidades->pluck('id'))
                        ->get();
       

        $servicos = $servicos->where('unidade.status','=','Ativa')
                        ->where('licenca_validade','>',date('Y-m-d'))
                        ->where('tipo','primario')
                        ->where('situacao','<>','arquivado');

        return view('cliente.lista-servicos')
                    ->with(
                        [
                            'servicos'=>$servicos,
                            'title'=>'Serviços com licenças vigentes',
                        ]);
    }

    public function listaVencidos()
    {
        

        $user = User::find(Auth::id());
        $servicos = Servico::with('unidade','empresa','responsavel')
                            ->whereIn('empresa_id',$user->empresas->pluck('id'))
                            ->orWhereIn('unidade_id', $user->unidades->pluck('id'))
                            ->get();
        
        $servicos = $servicos->where('unidade.status','=','Ativa')
                            ->where('licenca_validade','<',date('Y-m-d'))
                            ->where('tipo','=','primario')
                            ->where('situacao','<>','arquivado');

       


        return view('cliente.lista-servicos')
                    ->with(
                        [
                            'servicos'=>$servicos,
                            'title'=>'Serviços com licenças vencidas',
                        ]);
    }

    public function listaVencer()
    {
         $user = User::find(Auth::id());
        $servicos = Servico::with('unidade','empresa','responsavel')
                            ->whereIn('empresa_id',$user->empresas->pluck('id'))
                            ->orWhereIn('unidade_id', $user->unidades->pluck('id'))
                            ->get();

        $servicos = $servicos->where('licenca_validade','<',\Carbon\Carbon::today()->addDays(60))
                            ->where('situacao','=','finalizado'); 

        return view('cliente.lista-servicos')
                    ->with(
                        [
                            'servicos'=>$servicos,
                            'title'=>'Serviços com licenças a vencer',
                        ]);
    }

    public function listaInativo()
    {
         $user = User::find(Auth::id());
        $servicos = Servico::with('unidade','empresa','responsavel')
                            ->whereIn('empresa_id',$user->empresas->pluck('id'))
                            ->orWhereIn('unidade_id', $user->unidades->pluck('id'))
                            ->get();

        $servicos = $servicos->where('unidade.status','=','Inativa');

        return view('cliente.lista-servicos')
                    ->with(
                        [
                            'servicos'=>$servicos,
                            'title'=>'Serviços de unidades inativas',
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

        return redirect()->route('cliente.servico.show', $request->servico_id);
    }

    public function interacoes($id)
    {
        $interacoes = Historico::where('servico_id',$id)->orderBy('created_at','desc')->get();

        return view('cliente.lista-interacoes')->with('interacoes',$interacoes);
    }


    public function getServicosCliente()
    {
        $user = User::find(Auth::id());

        $servicos = Servico::whereIn('empresa_id',$user->empresas->pluck('id'))
                            ->orWhereIn('unidade_id', $user->unidades->pluck('id'))
                            ->get();

        return $servicos;
    }

    public function getPendenciasCliente()
    {
        $pendencias = Pendencia::with('servico','unidade')
    						->where('responsavel_id', Auth::id())
    						// ->whereIn('servico_id', $servicos)
    						// ->orWhere('responsavel_id',Auth::id())
    						->get();

        	
        	return $pendencias;
    }


    public function editarUsuario()
    {   
        
        $id = Auth::id();

        $usuario = User::with('empresas','unidades')->find($id);
        $empresas = Empresa::pluck('nomeFantasia','id');
        $unidades = Unidade::pluck('nomeFantasia','id');
        
        $access = UserAccess::with('empresa','unidade')->where('user_id',$id)->get();

        
        
        return view('admin.editar-usuario')
        ->with([
            'usuario'=>$usuario,
            'empresas'=>$empresas,
            'unidades'=>$unidades,
            'user_access'=>$access,
        ]);
    }

    public function updateUsuario(Request $request)
    {

        

        $id = Auth::id();
        
        $usuario = User::find($id);

        
        if($request->password!=null)
        {
            $usuario->password = Hash::make($request->password);
        }

        
        $usuario->name      =   $request->name;
        $usuario->email     =   $request->email;
        // $usuario->privileges=   $request->privileges;

        
        // $usuario->acesso_empresa()->sync($request->empresas_user_access);
        // $usuario->acesso_unidade    ()->sync($request->unidades_user_access);

        $usuario->save();


        
        return $this->index();
                    
    }

    public function getUnidadesList()
    {
        $unidadesList = Unidade::where('empresa_id', UserAccess::where('user_id',Auth::id())->pluck('empresa_id'))->pluck('id');

        return $unidadesList;
    }

    

    
}
