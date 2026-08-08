<?php

namespace App\Http\Controllers;

if (!function_exists('App\Http\Controllers\cleanObsText')) {
    function cleanObsText($text) {
        if (empty($text)) return '';
        $clean = html_entity_decode($text, ENT_QUOTES | ENT_HTML5, 'UTF-8');
        $clean = str_replace(["\xc2\xa0", '&nbsp;'], ' ', $clean);
        $clean = preg_replace('/<br\s*\/?>/i', "\n", $clean);
        $clean = preg_replace('/<\/p>/i', "\n", $clean);
        $clean = strip_tags($clean);
        $clean = str_replace(["\r\n", "\r"], "\n", $clean);
        $lines = array_map('trim', explode("\n", $clean));
        $filteredLines = [];
        $prevEmpty = false;
        foreach ($lines as $line) {
            if ($line === '') {
                if (!$prevEmpty) {
                    $filteredLines[] = '';
                    $prevEmpty = true;
                }
            } else {
                $filteredLines[] = $line;
                $prevEmpty = false;
            }
        }
        return trim(implode("\n", $filteredLines));
    }
}

use App\User;
use App\UserAccess;
use App\Models\Taxa;
use Carbon\Carbon;


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

        if (!$user || !count($user->empresas)) {
            return view('errors.403');
        }

        // Single query: load all services for this client (with relations eager-loaded)
        $servicos = $this->getServicosCliente($user);
        $pendencias = $this->getPendenciasCliente($user);

        // Derive unidades from already-loaded servicos — avoids a second SELECT on unidades
        $unidades = $this->getUnidadesCliente($user);

        // Compute licenca_status in PHP using the already-fetched collection (no extra queries)
        $today = date('Y-m-d');
        $licencasByUnidade = $servicos
            ->where('tipo', 'licencaOperacao')
            ->groupBy('unidade_id');

        foreach ($unidades as $unidade) {
            $licencas = $licencasByUnidade->get($unidade->id, collect());
            if ($licencas->isEmpty()) {
                $unidade->licenca_status = 'vencida';
            } else {
                $vencida = $licencas->first(function($l) use ($today) {
                    return $l->licenca_validade < $today;
                });
                $unidade->licenca_status = $vencida ? 'vencida' : 'vigente';
            }
        }

        // Valores de "solicitante" que identificam os serviços do cliente logado
        // (ele mesmo, se coordenador; ou o(s) coordenador(es) dele, se analista).
        $meuSolicitanteValores = $this->resolveMeusSolicitanteValues($user);

        return view('cliente.dashboard')
            ->with([
                'servicos'  => $servicos,
                'pendencias' => $pendencias,
                'unidades'  => $unidades,
                'meuSolicitanteValores' => $meuSolicitanteValores,
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
        $pendencia = Pendencia::with(['servico', 'responsavel', 'responsavelCliente'])->find($id);
        if (!$pendencia) {
            abort(404);
        }

        $arquivos = Arquivo::where('servico_id', $pendencia->servico_id)->with('user')->get();
        $historicos = Historico::where('pendencia_id', $pendencia->id)
            ->where('visibilidade', '!=', 'interno')
            ->with('user')
            ->orderBy('created_at', 'desc')
            ->get();

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
                'responsavel_cliente' => $pendencia->responsavelCliente->name ?? 'N/A',
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

        return view('cliente.detalhe-pendencia')->with([
            'pendencia' => $pendencia,
            'arquivos' => $arquivos,
            'historicos' => $historicos,
            'responsaveis' => $responsaveis,
        ]);
    }

    public function responderPendencia(Request $request, $id)
    {
        $pendencia = Pendencia::with('servico')->findOrFail($id);

        $request->validate([
            'observacoes' => 'required|string',
            'arquivo.*' => 'nullable|file|max:20480',
        ]);

        $user = Auth::user();

        // 1. Registrar histórico da resposta
        $cleanObservacao = trim(strip_tags(html_entity_decode($request->observacoes)));
        $historico = new Historico();
        $historico->pendencia_id = $pendencia->id;
        $historico->servico_id = $pendencia->servico_id;
        $historico->user_id = $user->id;
        $historico->observacoes = "Resposta do cliente: " . $cleanObservacao;
        $historico->visibilidade = 'cliente';
        $historico->created_at = \Carbon\Carbon::now();
        $historico->save();

        // 2. Anexar arquivos
        if ($request->hasFile('arquivo')) {
            $files = is_array($request->file('arquivo')) ? $request->file('arquivo') : [$request->file('arquivo')];
            foreach ($files as $file) {
                if ($file->isValid()) {
                    $name = uniqid(date('HisYmd'));
                    $extension = $file->getClientOriginalExtension();
                    $nameFile = "{$name}.{$extension}";
                    $upload = $file->storeAs('arquivos', $nameFile);

                    $arq = new Arquivo();
                    $arq->nome = "Anexo Pendência: " . ($file->getClientOriginalName() ?: $nameFile);
                    $arq->arquivo = $upload;
                    $arq->user_id = $user->id;
                    $arq->pendencia_id = $pendencia->id;
                    $arq->servico_id = $pendencia->servico_id;
                    if ($pendencia->servico) {
                        $arq->unidade_id = $pendencia->servico->unidade_id;
                        $arq->empresa_id = $pendencia->servico->empresa_id;
                    }
                    $arq->save();
                }
            }
        }

        // 3. Marcar data de resposta na pendência original
        $pendencia->respondida_em = \Carbon\Carbon::now();
        $pendencia->save();

        // 4. Criar pendência interna para coordenador "Avaliar resposta cliente"
        $coordenadorId = null;
        if ($pendencia->servico) {
            $coordenador = $pendencia->servico->coordenadores()->first();
            if ($coordenador) {
                $coordenadorId = $coordenador->id;
            } else {
                $coordenadorId = $pendencia->servico->responsavel_id ?: $pendencia->responsavel_id;
            }
        } else {
            $coordenadorId = $pendencia->responsavel_id;
        }

        $novaPendencia = new Pendencia();
        $novaPendencia->created_by = $user->id;
        $novaPendencia->servico_id = $pendencia->servico_id;
        $novaPendencia->pendencia = "Avaliar resposta do cliente (Pendência #" . $pendencia->id . ")";
        $novaPendencia->vencimento = \Carbon\Carbon::now()->addDays(2)->toDateString();
        $novaPendencia->responsavel_tipo = 'usuario';
        $novaPendencia->responsavel_id = $coordenadorId;
        $novaPendencia->status = 'pendente';
        $novaPendencia->observacoes = "O cliente " . $user->name . " respondeu à pendência '" . $pendencia->pendencia . "'. Observação: " . $cleanObservacao;
        $novaPendencia->etapa = 1;
        $novaPendencia->prioridade = 1;
        $novaPendencia->save();

        return redirect()->back()->with('success', 'Sua resposta foi enviada com sucesso e a equipe da Castro foi notificada!');
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

            $today = date('Y-m-d');
            $servicos = $servicos->filter(function ($servico) use ($today) {
                if (empty($servico->licenca_validade)) {
                    return false;
                }
                if (in_array($servico->situacao, ['arquivado', 'cancelado', 'nRenovado'])) {
                    return false;
                }
                if ($servico->unidade && $servico->unidade->status !== 'Ativa') {
                    return false;
                }

                $validade = \Carbon\Carbon::parse($servico->licenca_validade)->format('Y-m-d');
                return $validade >= $today;
            });
        } else {
            return view('errors.403');
        }

        return view('cliente.lista-servicos')
            ->with([
                'servicos' => $servicos,
                'title' => 'Serviços com Licenças Vigentes',
            ]);
    }

    public function listaVencidos()
    {
        $user = User::find(Auth::id());

        if (count($user->empresas)) {
            $servicos = $this->getServicosCliente();

            $today = date('Y-m-d');
            $servicos = $servicos->filter(function ($servico) use ($today) {
                if (empty($servico->licenca_validade)) {
                    return false;
                }
                if (in_array($servico->situacao, ['arquivado', 'cancelado', 'nRenovado'])) {
                    return false;
                }
                if ($servico->unidade && $servico->unidade->status !== 'Ativa') {
                    return false;
                }
                if ($servico->licenca_validade >= '2059-01-01') {
                    return false;
                }

                $validade = \Carbon\Carbon::parse($servico->licenca_validade)->format('Y-m-d');
                return $validade < $today;
            });
        } else {
            return view('errors.403');
        }

        return view('cliente.lista-servicos')
            ->with([
                'servicos' => $servicos,
                'title' => 'Serviços com Licenças Vencidas',
            ]);
    }

    public function listaVencer()
    {
        $user = User::find(Auth::id());

        if (count($user->empresas)) {
            $servicos = $this->getServicosCliente();

            $today = \Carbon\Carbon::today();
            $servicos = $servicos->filter(function ($servico) use ($today) {
                if ($servico->situacao !== 'finalizado') {
                    return false;
                }
                if (empty($servico->licenca_validade)) {
                    return false;
                }
                if ($servico->licenca_validade >= '2059-01-01') {
                    return false;
                }

                $dias = $servico->ativar_notificacao_renovacao
                    ? ($servico->dias_para_notificacao_renovacao ?? 180)
                    : 60;

                $dataLimite = $today->copy()->addDays($dias);
                $validade = \Carbon\Carbon::parse($servico->licenca_validade);

                return $validade->gte($today) && $validade->lte($dataLimite);
            });
        } else {
            return view('errors.403');
        }

        return view('cliente.lista-servicos')
            ->with([
                'servicos' => $servicos,
                'title' => 'Serviços com Licenças a Vencer',
            ]);
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

        $request->validate([
            'servico_id' => 'required|exists:servicos,id',
            'observacoes' => 'required|string',
        ]);

        $user = User::find(Auth::id());

        // Verificar se o cliente tem acesso ao serviço
        $servicosCliente = $this->getServicosCliente();
        $servico = $servicosCliente->where('id', $request->servico_id)->first();

        if (!$servico) {
            abort(403, 'Acesso não autorizado a este serviço.');
        }

        $cleanText = cleanObsText($request->observacoes);
        if (empty($cleanText)) {
            return redirect()->back()->with('error', 'Por favor, digite uma mensagem válida.');
        }

        // Criar o Histórico de Interação
        $interacao = new Historico();
        $interacao->servico_id = $servico->id;
        $interacao->observacoes = $cleanText;
        $interacao->user_id = Auth::id();
        $interacao->visibilidade = 'publico'; // Regra 4.2: Forçar 'publico' (nunca 'interno')
        $interacao->created_at = Carbon::now();
        $interacao->save();

        // Processar Menções permitidas para Cliente (Regra 4.2)
        $mentions = preg_match_all('/\B@[a-zA-Z\wÀ-ú]+\s\w+/', $cleanText, $matches);

        if ($mentions > 0) {
            // Obter coordenadores do serviço
            $coordenadoresIds = $servico->coordenadores()->pluck('users.id')->toArray();
            
            // Obter outros clientes com acesso às mesmas empresas do cliente logado
            $empresasIds = UserAccess::where('user_id', Auth::id())->pluck('empresa_id');
            $clientesEmpresaIds = UserAccess::whereIn('empresa_id', $empresasIds)->pluck('user_id')->toArray();

            $allowedUserIds = array_unique(array_merge($coordenadoresIds, $clientesEmpresaIds));

            $openAIService = new \App\Services\OpenAIService();
            $resumo = $openAIService->generateContextualSummary([
                'nome' => $servico->nome,
                'unidade' => $servico->unidade ? $servico->unidade->nomeFantasia : '',
                'situacao' => $servico->situacao,
                'tipo' => $servico->tipo
            ], $cleanText);

            $emailErrors = [];
            $webhookService = new \App\Services\WebhookService();

            foreach ($matches[0] as $m) {
                $uName = ltrim($m, "@");
                $mUser = User::where('name', 'like', '%' . $uName . '%')->first();

                if ($mUser && in_array($mUser->id, $allowedUserIds)) {
                    try {
                        $route = $mUser->privileges == 'admin' ? 'servicos.show' : 'cliente.servico.show';
                        $mUser->notify(new UserMentioned($servico, $route, $resumo));
                    } catch (\Exception $e) {
                        \Log::error('Erro na notificação de menção iniciada por cliente: ' . $e->getMessage());
                    }

                    $success = $webhookService->sendMentionEmail($mUser, $servico, $resumo, $cleanText);
                    if (!$success) {
                        $emailErrors[] = $mUser->name;
                    }
                }
            }
        }

        return redirect()->back()->with('success', 'Interação enviada com sucesso!');
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


    public function listaMeusAndamento()
    {
        $user = User::find(Auth::id());

        if ($user && count($user->empresas)) {
            // "Meus serviços": coordenador cliente -> solicitante é ele mesmo;
            // analista cliente -> solicitante é o coordenador dele.
            $servicos = $this->getServicosCliente($user, true);

            $servicos = $servicos->filter(function($servico) {
                return $servico->situacao === 'andamento';
            });
        } else {
            return view('errors.403');
        }

        return view('cliente.lista-servicos')
            ->with([
                'servicos' => $servicos,
                'title' => 'Meus Serviços em Andamento',
            ]);
    }

    public function listaStandBy()
    {
        $user = User::find(Auth::id());

        if ($user && count($user->empresas)) {
            $servicos = $this->getServicosCliente($user);

            $servicos = $servicos->where('situacao', 'standBy')
                ->where('situacao', '<>', 'arquivado');
        } else {
            return view('errors.403');
        }

        return view('cliente.lista-servicos')
            ->with([
                'servicos' => $servicos,
                'title' => 'Serviços em Stand-by',
            ]);
    }

    public function listaPendencias()
    {
        $user = User::find(Auth::id());

        if ($user && count($user->empresas)) {
            $pendencias = $this->getPendenciasCliente($user);
        } else {
            return view('errors.403');
        }

        return view('cliente.lista-pendencias')
            ->with([
                'pendencias' => $pendencias,
                'title' => 'Minhas Pendências em Aberto',
            ]);
    }

    public function getServicosCliente($user = null, $meu = null)
    {
        $user = $user ?: User::find(Auth::id());

        if (!$user || !count($user->empresas)) {
            return collect();
        }

        // Pluck once and reuse — avoids calling pluck() twice on the same relation
        $empresaIds = $user->empresas->pluck('id');
        $unidadeIds = Unidade::whereIn('empresa_id', $empresaIds)->pluck('id');

        $query = Servico::query()->where(function($q) use ($empresaIds, $unidadeIds) {
            $q->whereIn('empresa_id', $empresaIds)
              ->orWhereIn('unidade_id', $unidadeIds);
        });

        // Apply department filter if user has restrictions
        $depts = $user->departamentos;
        if (!empty($depts)) {
            $query->whereIn('departamento', $depts);
        }

        // "Meus serviços" filter: coordenador cliente -> solicitante é ele mesmo;
        // analista cliente -> solicitante tem que ser o coordenador dele.
        $meu = $meu ?? request()->boolean('meu');
        if ($meu) {
            $valores = $this->resolveMeusSolicitanteValues($user);
            $query->whereIn('solicitante', $valores ?: ['__nenhum__']);
        }

        return $query->with(['unidade', 'empresa', 'responsavel'])->get();
    }

    /**
     * Resolve os valores de "solicitante" (id do Solicitante e/ou nome legado)
     * que identificam os serviços "meus" para o cliente logado:
     * - cliente coordenador: apenas ele mesmo;
     * - cliente analista: o(s) coordenador(es) cliente da mesma empresa.
     */
    private function resolveMeusSolicitanteValues(User $user)
    {
        if ($user->is_coordinator) {
            $nomes = collect([$user->name]);
        } else {
            $empresaIds = $user->empresas->pluck('id');
            $coordenadorIds = UserAccess::whereIn('empresa_id', $empresaIds)->pluck('user_id');
            $nomes = User::whereIn('id', $coordenadorIds)
                ->where('privileges', 'cliente')
                ->where('is_coordinator', true)
                ->pluck('name');
        }

        $nomes = $nomes->filter()->map(fn($n) => trim($n))->unique()->values();
        if ($nomes->isEmpty()) {
            return [];
        }

        $nomesLower = $nomes->map(fn($n) => mb_strtolower($n));

        // Nomes de usuário costumam vir como "Empresa - Nome da Pessoa" (ex.: "Pague
        // Menos - Marcos Samuel"), enquanto o cadastro de Solicitante guarda só o nome
        // da pessoa (ex.: "Marcos Samuel"). Por isso, além da igualdade exata, também
        // consideramos quando o nome do Solicitante aparece contido no nome do usuário.
        $solicitantesEncontrados = \App\Models\Solicitante::get(['id', 'nome'])
            ->filter(function ($s) use ($nomesLower) {
                $solNome = mb_strtolower(trim((string) $s->nome));
                if ($solNome === '') {
                    return false;
                }
                return $nomesLower->contains(function ($nome) use ($solNome) {
                    return $nome === $solNome || strpos($nome, $solNome) !== false;
                });
            });

        return $solicitantesEncontrados->pluck('id')->map(fn($id) => (string) $id)
            ->merge($solicitantesEncontrados->pluck('nome'))
            ->merge($nomes)
            ->filter()
            ->unique()
            ->values()
            ->toArray();
    }

    public function getUnidadesCliente($user = null)
    {
        $user = $user ?: User::find(Auth::id());

        if (!$user || !count($user->empresas)) {
            return collect();
        }

        // Select only the columns needed (map + licenca_status computation)
        return Unidade::whereIn('empresa_id', $user->empresas->pluck('id'))
            ->select(['id', 'empresa_id', 'codigo', 'nomeFantasia', 'cidade', 'uf', 'latitude', 'longitude', 'status'])
            ->get();
    }

    public function getPendenciasCliente($user = null)
    {
        $user = $user ?: User::find(Auth::id());
        $depts = $user ? $user->departamentos : [];
        $userId = $user ? $user->id : Auth::id();

        // Scope pendências to the client's companies to avoid pulling other tenants' data
        $empresaIds = $user ? $user->empresas->pluck('id') : collect();
        $unidadeIds = $empresaIds->isNotEmpty()
            ? Unidade::whereIn('empresa_id', $empresaIds)->pluck('id')
            : collect();

        $query = Pendencia::with(['servico', 'unidade', 'responsavel', 'responsavelCliente'])
            ->where(function($q) use ($userId) {
                $q->where('responsavel_cliente_id', $userId)
                  ->orWhere(function($q2) use ($userId) {
                      $q2->where('responsavel_tipo', 'cliente')
                         ->where('responsavel_id', $userId);
                  });
            })
            ->where(function($q) use ($empresaIds, $unidadeIds) {
                $q->whereHas('servico', function($sq) use ($empresaIds, $unidadeIds) {
                    $sq->whereIn('empresa_id', $empresaIds)
                       ->orWhereIn('unidade_id', $unidadeIds);
                });
            });

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


    public function usersList(Request $request)
    {
        $user = User::find(Auth::id());
        $empresasIds = UserAccess::where('user_id', $user->id)->pluck('empresa_id');

        // Se for passado um servico_id, priorizar coordenadores do serviço + clientes das mesmas empresas
        $servicoId = $request->query('servico_id');
        if ($servicoId) {
            $servico = Servico::find($servicoId);
            if ($servico) {
                $coordenadores = $servico->coordenadores()->get();
                $outrosClientes = User::where('active', 1)
                    ->where('privileges', 'cliente')
                    ->whereIn('id', UserAccess::whereIn('empresa_id', $empresasIds)->pluck('user_id'))
                    ->get();
                
                $users = $coordenadores->merge($outrosClientes)->unique('id');
            } else {
                $users = User::where('active', 1)->whereIn('id', UserAccess::whereIn('empresa_id', $empresasIds)->pluck('user_id'))->get();
            }
        } else {
            // Apenas administradores/coordenadores + outros clientes das mesmas empresas
            $coordenadores = User::where('active', 1)->where('is_coordinator', 1)->get();
            $outrosClientes = User::where('active', 1)
                ->where('privileges', 'cliente')
                ->whereIn('id', UserAccess::whereIn('empresa_id', $empresasIds)->pluck('user_id'))
                ->get();
            $users = $coordenadores->merge($outrosClientes)->unique('id');
        }

        foreach ($users as $u) {
            $u->name = "@" . $u->name . " ";
        }

        return json_encode($users->values());
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

    public function uploadArquivo(Request $request)
    {
        $request->validate([
            'nome' => 'required|string|max:255',
            'arquivo' => 'required|file|max:20480',
        ]);

        $a = new Arquivo();
        if ($request->hasFile('arquivo') && $request->file('arquivo')->isValid()) {
            $name = uniqid(date('HisYmd'));
            $extension = $request->file('arquivo')->getClientOriginalExtension();
            $nameFile = "{$name}.{$extension}";
            $upload = $request->file('arquivo')->storeAs('arquivos', $nameFile);
            $a->arquivo = $upload;
        }

        $a->nome = $request->nome;
        $a->user_id = Auth::id();
        if ($request->unidade_id) {
            $a->unidade_id = $request->unidade_id;
        }
        if ($request->empresa_id) {
            $a->empresa_id = $request->empresa_id;
        }
        if ($request->servico_id) {
            $a->servico_id = $request->servico_id;
        }
        $a->save();

        return redirect()->back()->with('success', 'Arquivo enviado com sucesso!');
    }

}
