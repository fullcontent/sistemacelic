<?php

namespace App\Http\Controllers;

use App\User;
use App\UserAccess;
use App\Models\Taxa;


use App\Models\Empresa;
use App\Models\Servico;
use App\Models\Unidade;
use App\Models\Historico;

use App\Models\Arquivo;

use App\Models\Pendencia;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;


use App\Notifications\UserMentioned;
use Illuminate\Support\Facades\Notification;



class ClienteController extends Controller
{
    //



    public function __construct()
    {

        $this->middleware('auth');

    }

    public function index()
    {

        $user = User::find(Auth::id());

        if (!count($user->empresas)) {
            return view('errors.403');
        } else {
            $servicos = $this->getServicosCliente();
            $pendencias = $this->getPendenciasCliente();
        }

        return view('cliente.dashboard')
            ->with([
                'servicos' => $servicos,
                'pendencias' => $pendencias,


            ]);
    }

    public function empresas()
    {

        $user = User::find(Auth::id());

        $empresas = $user->empresas;

        return view('cliente.lista-empresas')->with('empresas', $empresas);

    }

    public function empresaShow($id)
    {

        $empresa = Empresa::find($id);
        return view('cliente.detalhe-empresa')
            ->with([
                'dados' => $empresa,
                'servicos' => $empresa->servicos,
                'taxas' => $empresa->taxas,
                'route' => 'empresas.edit',
            ]);
    }

    public function showPendencia($id)
    {
        $pendencia = Pendencia::find($id);
        $arquivos = Arquivo::where('servico_id', $pendencia->servico_id)->get();
        $responsaveis = User::orderBy('name')->where('active', 1)->pluck('name', 'id')->toArray();

        return view('cliente.detalhe-pendencia')->with(
            [
                'pendencia' => $pendencia,
                'arquivos' => $arquivos,
                'responsaveis' => $responsaveis,
            ]
        );
    }

    public function unidadeShow($id)
    {


        $unidade = Unidade::find($id);

        $access = Unidade::whereIn('empresa_id', UserAccess::where('user_id', Auth::id())->pluck('empresa_id'))->get();


        if ($access->pluck('id')->contains($id)) {
            return view('cliente.detalhe-empresa')
                ->with([
                    'dados' => $unidade,
                    'servicos' => $unidade->servicos,
                    'taxas' => $unidade->taxas,
                    'route' => 'unidades.edit',
                ]);
        } else {
            return view('errors.403');
        }




    }

    public function empresaUnidades($id)
    {
        $unidades = Unidade::with('empresa')->where('empresa_id', '=', $id)->get();
        $access = UserAccess::where('user_id', Auth::id())->whereNull('unidade_id')->get();

        if ($access->pluck('empresa_id')->contains($id)) {
            return view('cliente.lista-unidades')->with('unidades', $unidades);
        } else {
            return view('errors.403');
        }





    }

    public function unidades()
    {
        $user = User::find(Auth::id());

        $unidades = $this->getUnidadesCliente();

        return view('cliente.lista-unidades')->with('unidades', $unidades);
    }


    public function servicos()
    {
        $user = User::find(Auth::id());

        if (count($user->empresas)) {
            $servicos = $this->getServicosCliente();
            $servicos = $servicos->where('situacao', '<>', 'arquivado');
        } else {
            return view('errors.403');
        }





        return view('cliente.lista-servicos-geral')
            ->with('servicos', $servicos)
            ->with('title', 'Listando todos os serviços');


    }

    public function servicoShow($id)
    {


        $servico = Servico::find($id);

        if ($servico->unidade_id) {

            $dados = $servico->unidade;
            $route = 'unidades.edit';
        } else {
            $dados = $servico->empresa;
            $route = 'empresas.edit';
        }





        return view('cliente.detalhe-servico')
            ->with([
                'servico' => $servico,
                'dados' => $dados,
                'route' => $route,
                'taxas' => $servico->taxas,
                'pendencias' => $servico->pendencias,
            ]);
    }

    public function showTaxa(Request $request)
    {
        $taxa = Taxa::find($request->taxa);

        return view('cliente.detalhe-taxa')
            ->with([
                'taxa' => $taxa,
            ]);
    }


    public function listaAndamento()
    {

        $user = User::find(Auth::id());

        if (count($user->empresas)) {
            $servicos = $this->getServicosCliente();


            $servicos = $servicos->where('situacao', '=', 'andamento')
                ->where('situacao', '<>', 'arquivado');

        } else {
            return view('errors.403');
        }



        return view('cliente.lista-servicos')
            ->with(
                [
                    'servicos' => $servicos,
                    'title' => 'Serviços em Andamento',
                ]
            );
    }

    public function listaFinalizados()
    {

        $user = User::find(Auth::id());


        if (count($user->empresas)) {
            $servicos = $this->getServicosCliente();

            $servicos = $servicos->where('situacao', '=', 'finalizado')
                ->where('situacao', '<>', 'arquivado');
        } else {
            return view('errors.403');
        }



        return view('cliente.lista-servicos')
            ->with(
                [
                    'servicos' => $servicos,
                    'title' => 'Serviços Finalizados',
                ]
            );
    }

    public function listaVigentes()
    {

        $user = User::find(Auth::id());



        if (count($user->empresas)) {

            $servicos = $this->getServicosCliente();


            $servicos = $servicos->where('unidade.status', '=', 'Ativa')
                ->where('licenca_validade', '>', date('Y-m-d'))
                ->where('tipo', 'licencaOperacao')
                ->where('situacao', '<>', 'arquivado');
        } else {
            return view('errors.403');
        }



        return view('cliente.lista-servicos')
            ->with(
                [
                    'servicos' => $servicos,
                    'title' => 'Serviços com licenças vigentes',
                ]
            );
    }

    public function listaVencidos()
    {


        $user = User::find(Auth::id());



        if (count($user->empresas)) {
            $servicos = $this->getServicosCliente();

            $servicos = $servicos->where('unidade.status', '=', 'Ativa')
                ->where('licenca_validade', '<', date('Y-m-d'))
                ->where('tipo', '=', 'licencaOperacao')
                ->where('situacao', '<>', 'arquivado');
        } else {
            return view('errors.403');
        }






        return view('cliente.lista-servicos')
            ->with(
                [
                    'servicos' => $servicos,
                    'title' => 'Serviços com licenças vencidas',
                ]
            );
    }

    public function listaVencer()
    {
        $user = User::find(Auth::id());


        if (count($user->empresas)) {
            $servicos = $this->getServicosCliente();

            $servicos = $servicos->where('licenca_validade', '<', \Carbon\Carbon::today()->addDays(60))
                ->where('situacao', '=', 'finalizado');
        } else {
            return view('errors.403');
        }



        return view('cliente.lista-servicos')
            ->with(
                [
                    'servicos' => $servicos,
                    'title' => 'Serviços com licenças a vencer',
                ]
            );
    }

    public function listaInativo()
    {
        $user = User::find(Auth::id());


        if (count($user->empresas)) {
            $servicos = $this->getServicosCliente();

            $servicos = $servicos->where('unidade.status', '=', 'Inativa');
        } else {
            return view('errors.403');
        }


        return view('cliente.lista-servicos')
            ->with(
                [
                    'servicos' => $servicos,
                    'title' => 'Serviços de unidades inativas',
                ]
            );
    }

    public function salvarInteracao(Request $request)
    {

        $validator = Validator::make($request->all(), [

            'observacoes' => 'required',

        ])->validate();

        $interacao = new Historico;

        $interacao->servico_id = $request->servico_id;
        $interacao->observacoes = $request->observacoes;
        $interacao->user_id = Auth::id();

        $interacao->save();
        $servico = Servico::find($request->servico_id);

        //Notify users
        $mentions = preg_match_all('[\B@\w+\s\w+]', $request->observacoes, $users);


        if ($mentions > 0) {

            foreach ($users as $users2) {

                foreach ($users2 as $u) {
                    $u = ltrim($u, "@");

                    $user = User::where('name', 'like', '%' . $u . '%')->first();

                    if ($user) {
                        if ($user->privileges == 'admin') {
                            $route = 'servicos.show';
                        } elseif ($user->privileges == 'cliente') {
                            $route = 'cliente.servico.show';
                        }


                        Notification::send($user, new UserMentioned($servico, $route));
                    }
                }
            }
        }


        return redirect()->route('cliente.servico.show', $request->servico_id);
    }

    public function interacoes($id)
    {
        $interacoes = Historico::where('servico_id', $id)->orderBy('created_at', 'desc')->get();

        return view('cliente.lista-interacoes')->with('interacoes', $interacoes);
    }


    public function getServicosCliente()
    {
        $user = User::find(Auth::id());


        if (count($user->empresas)) {

            $unidades = Unidade::where('empresa_id', $user->empresas->pluck('id'))->pluck('id');

            $servicos = Servico::orWhereIn('empresa_id', $user->empresas->pluck('id'))
                ->orWhereIn('unidade_id', $unidades)
                ->get();

            return $servicos;
        } else {
            $servicos = null;
            return $servicos;
        }

    }

    public function getUnidadesCliente()
    {
        $user = User::find(Auth::id());

        if (count($user->empresas)) {
            $unidades = Unidade::where('empresa_id', $user->empresas->pluck('id'))->get();
            return $unidades;
        } else {
            $unidades = [];
            return $unidades;
        }



    }

    public function getPendenciasCliente()
    {
        $pendencias = Pendencia::with('servico', 'unidade')
            ->where('responsavel_id', Auth::id())
            // ->whereIn('servico_id', $servicos)
            // ->orWhere('responsavel_id',Auth::id())
            ->get();


        return $pendencias;
    }


    public function editarUsuario()
    {

        $id = Auth::id();

        $usuario = User::with('empresas', 'unidades')->find($id);
        $empresas = Empresa::pluck('nomeFantasia', 'id');
        $unidades = Unidade::pluck('nomeFantasia', 'id');

        $access = UserAccess::with('empresa', 'unidade')->where('user_id', $id)->get();



        return view('admin.editar-usuario')
            ->with([
                'usuario' => $usuario,
                'empresas' => $empresas,
                'unidades' => $unidades,
                'user_access' => $access,
            ]);
    }

    public function updateUsuario(Request $request)
    {



        $id = Auth::id();

        $usuario = User::find($id);


        if ($request->password != null) {
            $usuario->password = Hash::make($request->password);
        }


        $usuario->name = $request->name;
        $usuario->email = $request->email;
        // $usuario->privileges=   $request->privileges;


        // $usuario->acesso_empresa()->sync($request->empresas_user_access);
        // $usuario->acesso_unidade    ()->sync($request->unidades_user_access);

        $usuario->save();



        return $this->index();

    }

    public function getUnidadesList()
    {
        $unidadesList = Unidade::where('empresa_id', UserAccess::where('user_id', Auth::id())->pluck('empresa_id'))->pluck('id');

        return $unidadesList;
    }


    public function usersList()
    {
        $users = User::all();

        foreach ($users as $u) {

            $u->name = "@" . $u->name . " ";
        }

        return json_encode($users);
    }



}
