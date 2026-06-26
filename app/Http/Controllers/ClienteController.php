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
            $unidades = $this->getUnidadesCliente();

            if ($unidades && $servicos) {
                foreach ($unidades as $unidade) {
                    $licencas = $servicos->where('unidade_id', $unidade->id)->where('tipo', 'licencaOperacao');
                    if ($licencas->isEmpty()) {
                        $unidade->licenca_status = 'vencida';
                    } else {
                        $tem_vencida = false;
                        foreach ($licencas as $licenca) {
                            if ($licenca->licenca_validade < date('Y-m-d')) {
                                $tem_vencida = true;
                                break;
                            }
                        }
                        $unidade->licenca_status = $tem_vencida ? 'vencida' : 'vigente';
                    }
                }
            }
        }

        return view('cliente.dashboard')
            ->with([
                'servicos' => $servicos,
                'pendencias' => $pendencias,
                'unidades' => $unidades,
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
        $pendencia = Pendencia::with('servico', 'responsavel')->find($id);
        if (!$pendencia) {
            abort(404);
        }

        $arquivos = Arquivo::where('servico_id', $pendencia->servico_id)->with('user')->get();

        if (request()->ajax()) {
            $responsabilidadeMap = ['usuario' => 'Castro', 'cliente' => 'Cliente', 'op' => 'Orgão Público'];
            return response()->json([
                'id' => $pendencia->id,
                'etapa' => $pendencia->etapa,
                'os' => $pendencia->servico->os ?? 'N/A',
                'pendencia' => $pendencia->pendencia,
                'status' => $pendencia->status,
                'responsabilidade' => $responsabilidadeMap[$pendencia->responsavel_tipo] ?? $pendencia->responsavel_tipo,
                'responsavel' => $pendencia->responsavel->name ?? 'N/A',
                'vencimento' => $pendencia->vencimento ? \Carbon\Carbon::parse($pendencia->vencimento)->format('d/m/Y') : 'N/A',
                'observacoes' => $pendencia->observacoes ?? '',
                'arquivos' => $arquivos->map(function($a) {
                    return [
                        'nome' => $a->nome,
                        'user_name' => $a->user->name ?? 'N/A',
                        'download_url' => route('cliente.arquivo.download', $a->id)
                    ];
                })
            ]);
        }

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
        abort(403, 'Acesso não autorizado.');
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
        if (!Auth::user()->permitir_interacoes) {
            return redirect()->back()->with('error', 'Você não tem permissão para realizar interações.');
        }

        $validator = Validator::make($request->all(), [

            'observacoes' => 'required',

        ])->validate();

        $interacao = new Historico;

        $interacao->servico_id = $request->servico_id;
        $interacao->observacoes = $request->observacoes;
        $interacao->user_id = Auth::id();

        $interacao->save();
        $servico = Servico::with('unidade')->find($request->servico_id);

        $mentions = preg_match_all('/\B@[a-zA-Z\wÀ-ú]+\s\w+/', $request->observacoes, $users);

        if ($mentions > 0) {
            $openAIService = new \App\Services\OpenAIService();
            $resumo = $openAIService->generateContextualSummary([
                'nome' => $servico->nome,
                'unidade' => $servico->unidade ? $servico->unidade->nomeFantasia : 'N/A',
                'situacao' => $servico->situacao,
                'tipo' => $servico->tipo
            ], $request->observacoes);

            $emailErrors = [];
            $webhookService = new \App\Services\WebhookService();

            foreach ($users[0] as $u) {
                $u = ltrim($u, "@");

                $user = User::where('name', 'like', '%' . $u . '%')->first();
                if ($user) {
                    // 1. Notificação Padrão (Sininho)
                    try {
                        $route = $user->privileges == 'admin' ? 'servicos.show' : 'cliente.servico.show';
                        $user->notify(new UserMentioned($servico, $route, $resumo));
                    } catch (\Exception $e) {
                        \Log::error('ClienteController: Erro na notificação interna: ' . $e->getMessage());
                    }

                    // 2. Email via Webhook
                    $success = $webhookService->sendMentionEmail($user, $servico, $resumo, $request->observacoes);
                    if (!$success) {
                        $emailErrors[] = $user->name;
                    }
                }
            }

            if (!empty($emailErrors)) {
                $names = implode(', ', $emailErrors);
                session()->flash('error', "A interação foi salva, mas ocorreu um erro ao enviar notificação para: {$names}.");
            } else {
                session()->flash('success', "Interação salva e notificações enfileiradas com sucesso.");
            }
        } else {
            session()->flash('success', "Interação salva com sucesso.");
        }


        return redirect()->route('cliente.servico.show', $request->servico_id);
    }

    public function interacoes($id)
    {
        $servico = Servico::findOrFail($id);
        $interacoes = Historico::where('servico_id', $id)
            ->where('observacoes', 'not like', '@%')
            ->where('visibilidade', '!=', 'interno')
            ->orderBy('created_at', 'desc')
            ->get();

        return view('cliente.lista-interacoes')->with([
            'interacoes' => $interacoes,
            'servico' => $servico
        ]);
    }


    public function getServicosCliente()
    {
        $user = User::find(Auth::id());

        if (count($user->empresas)) {
            $unidades = Unidade::where('empresa_id', $user->empresas->pluck('id'))->pluck('id');

            $query = Servico::query();

            // Scope query by companies and units first
            $query->where(function($q) use ($user, $unidades) {
                $q->whereIn('empresa_id', $user->empresas->pluck('id'))
                  ->orWhereIn('unidade_id', $unidades);
            });

            // Apply department filter if user has restrictions
            $depts = $user->departamentos;
            if (!empty($depts)) {
                $query->whereIn('departamento', $depts);
            }

            return $query->get();
        } else {
            return null;
        }
    }

    public function getUnidadesCliente()
    {
        $user = User::find(Auth::id());

        if (count($user->empresas)) {
            $unidades = Unidade::where('empresa_id', $user->empresas->pluck('id'))->get();
            return $unidades;
        } else {
            return [];
        }
    }

    public function getPendenciasCliente()
    {
        $user = User::find(Auth::id());
        $depts = $user->departamentos;
        
        $query = Pendencia::with('servico', 'unidade')
            ->where('responsavel_id', Auth::id());
            
        if (!empty($depts)) {
            $query->whereHas('servico', function($q) use ($depts) {
                $q->whereIn('departamento', $depts);
            });
        }
        
        return $query->get();
    }


    public function editarUsuario()
    {
        abort(403, 'Edição de perfil desabilitada para clientes.');
    }

    public function updateUsuario(Request $request)
    {
        abort(403, 'Edição de perfil desabilitada para clientes.');
    }

    public function getUnidadesList()
    {
        $unidadesList = Unidade::where('empresa_id', UserAccess::where('user_id', Auth::id())->pluck('empresa_id'))->pluck('id');

        return $unidadesList;
    }


    public function usersList()
    {
        $users = User::where('active', 1)->get();

        foreach ($users as $u) {

            $u->name = "@" . $u->name . " ";
        }

        return json_encode($users);
    }


    public function arquivosDigitais(Request $request)
    {
        if (app()->bound('debugbar')) {
            app('debugbar')->disable();
        }

        $user = User::find(Auth::id());
        $empresasIds = UserAccess::where('user_id', $user->id)->pluck('empresa_id');
        
        // Obter todas as unidades ordenadas por nome para o dropdown
        $unidades = Unidade::whereIn('empresa_id', $empresasIds)->orderBy('nomeFantasia')->get();

        // Determinar a unidade selecionada (padrão: primeira unidade)
        $selectedUnitId = $request->query('unidade_id');
        if (!$selectedUnitId && $unidades->isNotEmpty()) {
            $selectedUnitId = $unidades->first()->id;
        }

        $selectedUnit = $selectedUnitId ? Unidade::find($selectedUnitId) : null;

        $todosArquivos = [];

        if ($selectedUnit) {
            $depts = $user->departamentos;

            // Buscar apenas os serviços da unidade selecionada que possuem algum anexo válido
            $queryServicos = Servico::where('unidade_id', $selectedUnit->id)
                ->where(function($q) {
                    $q->where(function($sub) {
                        $sub->whereNotNull('licenca_anexo')->where('licenca_anexo', '<>', '');
                    })->orWhere(function($sub) {
                        $sub->whereNotNull('laudo_anexo')->where('laudo_anexo', '<>', '');
                    })->orWhere(function($sub) {
                        $sub->whereNotNull('protocolo_anexo')->where('protocolo_anexo', '<>', '');
                    });
                });

            if (!empty($depts)) {
                $queryServicos->whereIn('departamento', $depts);
            }
            $servicos = $queryServicos->get();

            // Buscar apenas arquivos associados a esta unidade
            $queryArquivos = Arquivo::where('unidade_id', $selectedUnit->id)
                ->whereNotNull('arquivo')
                ->where('arquivo', '<>', '')
                ->with(['servico']);

            if (!empty($depts)) {
                $queryArquivos->where(function($q) use ($depts) {
                    $q->whereNull('servico_id')
                      ->orWhereHas('servico', function($sub) use ($depts) {
                          $sub->whereIn('departamento', $depts);
                      });
                });
            }
            $arquivos = $queryArquivos->get();

            // 1. Processar anexos dos serviços
            foreach ($servicos as $servico) {
                $unidCode = $selectedUnit->codigo;
                $unidName = $selectedUnit->nomeFantasia;

                if ($servico->licenca_anexo) {
                    $todosArquivos[] = [
                        'id' => $servico->id,
                        'nome' => 'Licença: ' . $servico->nome,
                        'tipo_arquivo' => 'licenca',
                        'arquivo' => $servico->licenca_anexo,
                        'unidade_id' => $selectedUnit->id,
                        'unidade_codigo' => $unidCode,
                        'unidade_name' => $unidName,
                        'servico_id' => $servico->id,
                        'servico_os' => $servico->os,
                        'servico_nome' => $servico->nome,
                        'servico_tipo' => $servico->tipo,
                        'emissao' => $servico->licenca_emissao,
                        'validade' => $servico->licenca_validade,
                        'tipo_licenca' => $servico->tipoLicenca,
                        'download_url' => '/cliente/arquivos/download/servico/licenca/' . $servico->id
                    ];
                }
                if ($servico->laudo_anexo) {
                    $todosArquivos[] = [
                        'id' => $servico->id,
                        'nome' => 'Laudo: ' . $servico->nome,
                        'tipo_arquivo' => 'laudo',
                        'arquivo' => $servico->laudo_anexo,
                        'unidade_id' => $selectedUnit->id,
                        'unidade_codigo' => $unidCode,
                        'unidade_name' => $unidName,
                        'servico_id' => $servico->id,
                        'servico_os' => $servico->os,
                        'servico_nome' => $servico->nome,
                        'servico_tipo' => $servico->tipo,
                        'emissao' => $servico->laudo_emissao,
                        'validade' => null,
                        'tipo_licenca' => null,
                        'download_url' => '/cliente/arquivos/download/servico/laudo/' . $servico->id
                    ];
                }
                if ($servico->protocolo_anexo) {
                    $todosArquivos[] = [
                        'id' => $servico->id,
                        'nome' => 'Protocolo: ' . $servico->nome,
                        'tipo_arquivo' => 'protocolo',
                        'arquivo' => $servico->protocolo_anexo,
                        'unidade_id' => $selectedUnit->id,
                        'unidade_codigo' => $unidCode,
                        'unidade_name' => $unidName,
                        'servico_id' => $servico->id,
                        'servico_os' => $servico->os,
                        'servico_nome' => $servico->nome,
                        'servico_tipo' => $servico->tipo,
                        'emissao' => $servico->protocolo_emissao,
                        'validade' => null,
                        'tipo_licenca' => null,
                        'download_url' => '/cliente/arquivos/download/servico/protocolo/' . $servico->id
                    ];
                }
            }

            // 2. Processar registros da tabela Arquivo
            foreach ($arquivos as $arquivo) {
                $unidCode = $selectedUnit->codigo;
                $unidName = $selectedUnit->nomeFantasia;

                $todosArquivos[] = [
                    'id' => $arquivo->id,
                    'nome' => $arquivo->nome,
                    'tipo_arquivo' => 'geral',
                    'arquivo' => $arquivo->arquivo,
                    'unidade_id' => $selectedUnit->id,
                    'unidade_codigo' => $unidCode,
                    'unidade_name' => $unidName,
                    'servico_id' => $arquivo->servico_id,
                    'servico_os' => $arquivo->servico ? $arquivo->servico->os : null,
                    'servico_nome' => $arquivo->servico ? $arquivo->servico->nome : null,
                    'servico_tipo' => $arquivo->servico ? $arquivo->servico->tipo : 'geral',
                    'emissao' => $arquivo->created_at,
                    'validade' => null,
                    'tipo_licenca' => null,
                    'download_url' => '/cliente/arquivos/download/arquivo/' . $arquivo->id
                ];
            }
        }

        // Agrupar por Tipo de Serviço para a unidade selecionada
        $arquivosPorTipoServico = [];
        $tiposNomes = [
            'licencaOperacao' => 'Licença de Operação',
            'nRenovaveis' => 'Licenças/Projetos não renováveis',
            'controleCertidoes' => 'Certidões',
            'controleTaxas' => 'Taxas',
            'facilitiesRealEstate' => 'Facilities/Real Estate',
            'geral' => 'Arquivos Gerais / Sem Serviço específico'
        ];

        foreach ($todosArquivos as $arq) {
            $tipo = $arq['servico_tipo'] ?: 'geral';
            $arquivosPorTipoServico[$tipo][] = $arq;
        }

        // Apenas Licenças da unidade selecionada
        $licencas = array_filter($todosArquivos, function($arq) {
            return $arq['tipo_arquivo'] === 'licenca';
        });

        $totalUnidades = count($unidades);

        return view('cliente.arquivos-digitais')->with([
            'unidades' => $unidades,
            'selectedUnit' => $selectedUnit,
            'arquivosPorTipoServico' => $arquivosPorTipoServico,
            'tiposNomes' => $tiposNomes,
            'licencas' => $licencas,
            'todosArquivos' => $todosArquivos,
            'totalUnidades' => $totalUnidades
        ]);
    }

    public function downloadServicoFile($tipo, $servico_id)
    {
        $empresasIds = UserAccess::where('user_id', Auth::id())->pluck('empresa_id');
        $unidadesIds = Unidade::whereIn('empresa_id', $empresasIds)->pluck('id');

        $user = User::find(Auth::id());
        $depts = $user->departamentos;

        $query = Servico::where('id', $servico_id)
            ->where(function($q) use ($unidadesIds, $empresasIds) {
                $q->whereIn('unidade_id', $unidadesIds)
                  ->orWhereIn('empresa_id', $empresasIds);
            });

        if (!empty($depts)) {
            $query->whereIn('departamento', $depts);
        }

        $servico = $query->first();

        if (!$servico) {
            abort(403, 'Acesso não autorizado ou serviço inexistente.');
        }

        switch ($tipo) {
            case 'licenca':
                $filename = $servico->licenca_anexo;
                $tipoNome = "Licença";
                break;
            case 'laudo':
                $filename = $servico->laudo_anexo;
                $tipoNome = "Laudo";
                break;
            case 'protocolo':
                $filename = $servico->protocolo_anexo;
                $tipoNome = "Protocolo";
                break;
            default:
                abort(404, 'Tipo de arquivo inválido.');
        }

        if (!$filename || !file_exists(public_path('uploads/'.$filename))) {
            abort(404, 'Arquivo físico não encontrado no servidor.');
        }

        $extension = pathinfo($filename, PATHINFO_EXTENSION);
        $unidadeNome = $servico->unidade ? $servico->unidade->nomeFantasia : 'Sem Unidade';
        $unidadeCodigo = $servico->unidade ? $servico->unidade->codigo : 'S-U';
        $arquivoNome = $tipoNome.' '.$unidadeCodigo.' - '.$unidadeNome.' - '.$servico->nome.'.'.$extension;

        return response()->download(public_path('uploads/'.$filename), $arquivoNome);
    }

    public function downloadArquivo($id)
    {
        $arquivo = Arquivo::find($id);
        if (!$arquivo) {
            abort(404, 'Arquivo não encontrado.');
        }

        $empresasIds = UserAccess::where('user_id', Auth::id())->pluck('empresa_id');
        $unidadesIds = Unidade::whereIn('empresa_id', $empresasIds)->pluck('id');

        $hasAccess = false;
        if ($arquivo->unidade_id && $unidadesIds->contains($arquivo->unidade_id)) {
            $hasAccess = true;
        } elseif ($arquivo->empresa_id && $empresasIds->contains($arquivo->empresa_id)) {
            $hasAccess = true;
        } elseif ($arquivo->servico_id) {
            $servico = Servico::find($arquivo->servico_id);
            if ($servico && ($unidadesIds->contains($servico->unidade_id) || $empresasIds->contains($servico->empresa_id))) {
                $hasAccess = true;
            }
        }

        if (!$hasAccess) {
            abort(403, 'Acesso não autorizado.');
        }

        $user = User::find(Auth::id());
        $depts = $user->departamentos;
        if (!empty($depts)) {
            if ($arquivo->servico_id) {
                $servico = Servico::find($arquivo->servico_id);
                if ($servico && !in_array($servico->departamento, $depts)) {
                    abort(403, 'Acesso não autorizado ao departamento deste arquivo.');
                }
            }
        }

        $filename = $arquivo->arquivo;
        if (!$filename || !file_exists(public_path('uploads/'.$filename))) {
            abort(404, 'Arquivo físico não encontrado no servidor.');
        }

        $extension = pathinfo($filename, PATHINFO_EXTENSION);
        $unidadeNome = $arquivo->unidade ? $arquivo->unidade->nomeFantasia : 'Sem Unidade';
        $unidadeCodigo = $arquivo->unidade ? $arquivo->unidade->codigo : 'S-U';
        $arquivoNome = $unidadeCodigo.' - '.$unidadeNome.' - '.$arquivo->nome.'.'.$extension;

        return response()->download(public_path('uploads/'.$filename), $arquivoNome);
    }

}
