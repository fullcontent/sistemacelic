<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\Proposta;
use App\Models\PropostaServico;
use App\Models\Unidade;
use App\Models\Servico;
use App\Models\ServicoLpu;
use App\Models\Historico;
use App\Models\ServicoFinanceiro;
use App\Models\Pendencia;
use App\Models\Solicitante;
use App\User;
use Carbon\Carbon;
use Auth;
use Dompdf\Dompdf;


class PropostasController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $hoje = Carbon::now();
        $periodo = $request->get('periodo', 'todos');
        $statusFiltro = $request->get('status');
        $vendedorFiltro = $request->get('vendedor');
        $solicitanteFiltro = $request->get('solicitante');

        $propostas = collect([]);

        // Base query for stats with standard exclusions
        $baseStatsQuery = Proposta::whereNotIn('empresa_id', [16])->where('status', '!=', 'Arquivada');

        $periodoLabel = "Todos os Registros";

        // Apply period filtering to stats queries
        if ($periodo == 'mes_vigente') {
            $baseStatsQuery->whereYear('created_at', $hoje->year)->whereMonth('created_at', $hoje->month);
            $periodoLabel = $hoje->translatedFormat('F Y');
        } elseif ($periodo == 'mes_anterior') {
            $mesAnterior = $hoje->copy()->subMonth();
            $baseStatsQuery->whereYear('created_at', $mesAnterior->year)->whereMonth('created_at', $mesAnterior->month);
            $periodoLabel = $mesAnterior->translatedFormat('F Y');
        } elseif (strpos($periodo, 'mes_') === 0) {
            // Handle historical months (e.g., mes_2024_02)
            $parts = explode('_', $periodo);
            if (count($parts) == 3) {
                $year = $parts[1];
                $month = $parts[2];
                $baseStatsQuery->whereYear('created_at', $year)->whereMonth('created_at', $month);
                $periodoLabel = Carbon::createFromDate($year, $month, 1)->translatedFormat('F Y');
            }
        } elseif ($periodo == 'ano_atual') {
            $baseStatsQuery->whereYear('created_at', $hoje->year);
            $periodoLabel = "Ano " . $hoje->year;
        }

        $stats = [];
        $stats['elaboracao_count'] = (clone $baseStatsQuery)->where('status', 'Revisando')->count();

        // Em Análise Stats
        $analiseQuery = (clone $baseStatsQuery)->where('status', 'Em análise');
        $stats['analise_count'] = (clone $analiseQuery)->count();

        $stats['analise_0_7'] = (clone $analiseQuery)
            ->where(function ($q) {
                $q->where(function ($sq) {
                    $sq->whereNotNull('sent_to_analysis_at')
                        ->where('sent_to_analysis_at', '>=', now()->subDays(7));
                })->orWhere(function ($sq) {
                    $sq->whereNull('sent_to_analysis_at')
                        ->where('created_at', '>=', now()->subDays(7));
                });
            })
            ->count();

        $stats['analise_8_15'] = (clone $analiseQuery)
            ->where(function ($q) {
                $q->where(function ($sq) {
                    $sq->whereNotNull('sent_to_analysis_at')
                        ->where('sent_to_analysis_at', '<', now()->subDays(7))
                        ->where('sent_to_analysis_at', '>=', now()->subDays(15));
                })->orWhere(function ($sq) {
                    $sq->whereNull('sent_to_analysis_at')
                        ->where('created_at', '<', now()->subDays(7))
                        ->where('created_at', '>=', now()->subDays(15));
                });
            })
            ->count();

        $stats['analise_15_plus'] = (clone $analiseQuery)
            ->where(function ($q) {
                $q->where(function ($sq) {
                    $sq->whereNotNull('sent_to_analysis_at')
                        ->where('sent_to_analysis_at', '<', now()->subDays(15));
                })->orWhere(function ($sq) {
                    $sq->whereNull('sent_to_analysis_at')
                        ->where('created_at', '<', now()->subDays(15));
                });
            })
            ->count();

        $stats['aprovadas_mes_count'] = (clone $baseStatsQuery)->where('status', 'Aprovada')->count();
        $stats['recusadas_count'] = (clone $baseStatsQuery)->where('status', 'Recusada')->count();

        $totalNoPeriodo = (clone $baseStatsQuery)->count();
        $totalAprovadasNoPeriodo = $stats['aprovadas_mes_count'];
        $stats['conversao'] = $totalNoPeriodo > 0 ? ($totalAprovadasNoPeriodo / $totalNoPeriodo) * 100 : 0;

        // Conversion Rate Comparison (Last Month)
        $mesAnterior = $hoje->copy()->subMonth();
        $baseStatsMesAnterior = Proposta::whereNotIn('empresa_id', [16])
            ->where('status', '!=', 'Arquivada')
            ->whereYear('created_at', $mesAnterior->year)
            ->whereMonth('created_at', $mesAnterior->month);

        $totalMesAnterior = (clone $baseStatsMesAnterior)->count();
        $aprovadasMesAnterior = (clone $baseStatsMesAnterior)->where('status', 'Aprovada')->count();
        $conversaoMesAnterior = $totalMesAnterior > 0 ? ($aprovadasMesAnterior / $totalMesAnterior) * 100 : 0;

        $stats['conversao_anterior'] = $conversaoMesAnterior;
        $stats['conversao_diff'] = $stats['conversao'] - $conversaoMesAnterior;

        // Goals Calculation (Constant 120k for now)
        $stats['meta_valor'] = 175000;

        // Revenue from approved proposals. 
        // Rule: If period is 'todos' or 'ano_atual', we show Meta for the current month only.
        $metaQuery = clone $baseStatsQuery;
        if ($periodo == 'todos' || $periodo == 'ano_atual') {
            $metaQuery = Proposta::whereNotIn('empresa_id', [16])
                ->where('status', '!=', 'Arquivada')
                ->whereYear('created_at', $hoje->year)
                ->whereMonth('created_at', $hoje->month);
            $stats['periodo_label'] = $hoje->translatedFormat('F Y');
        }

        $idsAprovadas = $metaQuery->where('status', 'Aprovada')->pluck('id');
        $valorAprovadoPeriodo = \App\Models\PropostaServico::whereIn('proposta_id', $idsAprovadas)->sum('valor');

        $stats['valor_aprovado'] = $valorAprovadoPeriodo;
        $stats['meta_percentual'] = $stats['meta_valor'] > 0 ? ($valorAprovadoPeriodo / $stats['meta_valor']) * 100 : 0;

        $stats['status_atual'] = $statusFiltro;
        $stats['periodo_atual'] = $periodo;
        $stats['periodo_label'] = $periodoLabel;
        $stats['vendedor_atual'] = $vendedorFiltro;
        $stats['solicitante_atual'] = $solicitanteFiltro;

        // Historical Months (Last 6 months)
        $mesesFiltro = [];
        for ($i = 0; $i < 7; $i++) {
            $date = $hoje->copy()->subMonths($i);
            $val = "mes_" . $date->year . "_" . $date->month;
            if ($i == 0)
                $val = 'mes_vigente';
            if ($i == 1)
                $val = 'mes_anterior';

            $mesesFiltro[$val] = $date->translatedFormat('F Y');
        }

        // Data for filters
        $vendedores = User::orderBy('name')->pluck('name', 'id');
        $users = $vendedores; // For the assign modal
        $solicitantes = Solicitante::orderBy('nome')->pluck('nome', 'id');
        $status_list = ['Em análise', 'Aprovada', 'Recusada', 'Arquivada', 'Revisando'];

        return view('admin.proposta.lista-propostas')
            ->with([
                'propostas' => $propostas,
                'stats' => $stats,
                'vendedores' => $vendedores,
                'users' => $users,
                'solicitantes' => $solicitantes,
                'status_list' => $status_list,
                'meses_filtro' => $mesesFiltro
            ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {


        $u = Proposta::pluck('id')->last();
        $ultimaProposta = $u + 1;

        $solicitantes = Solicitante::orderBy('nome')->get()->unique('nome')->pluck('nome', 'id');


        return view('admin.proposta.step1')->with(
            [
                'ultimaProposta' => $ultimaProposta,
                'solicitantes' => $solicitantes,
            ]
        );
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */

    public function step2($proposta)
    {

        $propostaServicos = PropostaServico::where('proposta_id', $proposta->id)->get();


        return view('admin.proposta.step2')->with(['proposta' => $proposta, 'propostaServicos' => $propostaServicos]);
    }

    public function step3(Request $request)
    {
        // Placeholder for step 3 logic if needed
    }


    public function store(Request $request)
    {



        // dd($request->all());

        if ($request->proposta_id) {
            $proposta = Proposta::find($request->proposta_id);

        } else {

            $proposta = new Proposta;

            // $proposta->id = $request->proposta_id;

            $proposta->unidade_id = $request->unidade_id;
            $proposta->status = "Revisando";
            $proposta->created_by = Auth::id();




            $unidade = Unidade::find($request->unidade_id);
            $proposta->empresa_id = $unidade->empresa_id;

            // $proposta->responsavel_id = $request->responsavel_id;
            $proposta->solicitante = $request->solicitante;

            $proposta->documentos = $request->documentos;
            $proposta->condicoesGerais = $request->condicoesGerais;
            $proposta->condicoesPagamento = $request->condicoesPagamento;
            $proposta->dadosPagamento = $request->dadosPagamento;




            //remove class and style attributes from coppied text

            $proposta->dadosPagamento = preg_replace('/(<[^>]+) style=("|\').*?("|\')/i', '$1', $proposta->dadosPagamento);
            $proposta->dadosPagamento = preg_replace('/(<[^>]+) class=("|\').*?("|\')/i', '$1', $proposta->dadosPagamento);


            $proposta->condicoesGerais = preg_replace('/(<[^>]+) style=("|\').*?("|\')/i', '$1', $proposta->condicoesGerais);
            $proposta->condicoesGerais = preg_replace('/(<[^>]+) class=("|\').*?("|\')/i', '$1', $proposta->condicoesGerais);

            $proposta->condicoesPagamento = preg_replace('/(<[^>]+) style=("|\').*?("|\')/i', '$1', $proposta->condicoesPagamento);
            $proposta->condicoesPagamento = preg_replace('/(<[^>]+) class=("|\').*?("|\')/i', '$1', $proposta->condicoesPagamento);

            $proposta->documentos = preg_replace('/(<[^>]+) style=("|\').*?("|\')/i', '$1', $proposta->documentos);
            $proposta->documentos = preg_replace('/(<[^>]+) class=("|\').*?("|\')/i', '$1', $proposta->documentos);


            $proposta->save();

        }




        // dump($proposta);

        foreach ($request->servico as $key => $s) {



            if (strlen($key) <= 1) {
                $propostaServico = new PropostaServico;
                $propostaServico->servico = $s['nome'];
                $propostaServico->escopo = $s['escopo'];
                $propostaServico->valor = $s['valor'];
                $propostaServico->posicao = $key;

                $propostaServico->proposta_id = $proposta->id;

                $propostaServico->responsavel_id = $s['responsavel_id'];

                $propostaServico->servicoLpu_id = $s['id'];

                $propostaServico->save();

                // dump($propostaServico);
            }




            if (strlen($key) > 1) {
                // dump($key);

                $propostaServicoSub = new PropostaServico;
                $propostaServicoSub->servico = $s['nome'];
                $propostaServicoSub->escopo = $s['escopo'];
                $propostaServicoSub->valor = $s['valor'];
                $propostaServicoSub->servicoLpu_id = $s['id'];
                $propostaServicoSub->posicao = substr($key, -1);
                $propostaServicoSub->servicoPrincipal = $propostaServico->id;

                $propostaServicoSub->proposta_id = $proposta->id;

                $propostaServicoSub->responsavel_id = $s['responsavel_id'];


                $propostaServicoSub->save();

                // dump($propostaServicoSub);



            }





        }




        return redirect(route('proposta.edit', $proposta->id));


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

        $proposta = Proposta::with('servicos')->find($id);
        $this->reOrderServices($proposta->id); //Reordenar indice dos servicos da proposta


        return view('admin.proposta.editar-proposta')->with(['proposta' => $proposta]);
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

        $proposta = Proposta::find($id);

        $proposta->dadosPagamento = $request->dadosPagamento;
        $proposta->condicoesGerais = $request->condicoesGerais;
        $proposta->condicoesPagamento = $request->condicoesPagamento;
        $proposta->documentos = $request->documentos;



        //remove class and style attributes from coppied text

        $proposta->dadosPagamento = preg_replace('/(<[^>]+) style=("|\').*?("|\')/i', '$1', $proposta->dadosPagamento);
        $proposta->dadosPagamento = preg_replace('/(<[^>]+) class=("|\').*?("|\')/i', '$1', $proposta->dadosPagamento);


        $proposta->condicoesGerais = preg_replace('/(<[^>]+) style=("|\').*?("|\')/i', '$1', $proposta->condicoesGerais);
        $proposta->condicoesGerais = preg_replace('/(<[^>]+) class=("|\').*?("|\')/i', '$1', $proposta->condicoesGerais);

        $proposta->condicoesPagamento = preg_replace('/(<[^>]+) style=("|\').*?("|\')/i', '$1', $proposta->condicoesPagamento);
        $proposta->condicoesPagamento = preg_replace('/(<[^>]+) class=("|\').*?("|\')/i', '$1', $proposta->condicoesPagamento);

        $proposta->documentos = preg_replace('/(<[^>]+) style=("|\').*?("|\')/i', '$1', $proposta->documentos);
        $proposta->documentos = preg_replace('/(<[^>]+) class=("|\').*?("|\')/i', '$1', $proposta->documentos);





        $proposta->save();

        return view('admin.proposta.editar-proposta')->with(['proposta' => $proposta]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {

        return "Deletendo";
        // $proposta = Proposta::find($id);
        // $proposta->delete();

        // foreach($proposta->servicos as $s)
        // {
        //     $servico = PropostaServico::find($s->id);
        //     $servico->delete();
        // }

        // return route('admin.proposta.index');


    }

    public function removerProposta($id)
    {

        $proposta = Proposta::find($id);


        //Verificar se já foram criados os serviços
        if ($proposta->servicos) {
            foreach ($proposta->servicos as $s) {

                //Arquivar servico
                if ($s->servicoCriado) {
                    // dump("Servico ja criado - ".$s->servicoCriado->os."");
                    // dump($s->servicoCriado->financeiro);


                    $servico = Servico::find($s->servicoCriado->id);

                    $servico->situacao = "arquivado";
                    $servico->save();
                    // dump($servico);
                }
            }
        }

        $proposta->status = "Arquivada";
        $proposta->save();

        // dump($proposta);

        return response()->json(['success' => true]);
    }

    public function editarServico(Request $request)
    {

        $servico = PropostaServico::find($request->servico_id);

        $servico->servico = $request->servico;
        $servico->escopo = $request->escopo;
        $servico->valor = $request->valor;
        $servico->save();


        return redirect(route('proposta.edit', $servico->proposta_id));


    }


    public function removerServico($id)
    {

        $servico = PropostaServico::find($id);

        if ($servico->financeiro) {
            $servico->servicoCriado->financeiro->delete();
        }

        if ($servico->servicoCriado) {

            if ($servico->servicoCriado->pendencias) {
                $servico->servicoCriado->pendencias->delete();
            }

            $servico->servicoCriado->delete();

        }



        $servico->delete();

        //Remover PropostaServico

        // $servico->delete();
        // //Remover Servico
        // $servico->servicoCriado->delete();
        // //Remover Pendencias
        // $servico->servicoCriado->pendencias->delete();
        // //Remover Financeiro
        // $servico->servicoCriado->financeiro->delete();



    }

    public function analisar($id)
    {
        $proposta = Proposta::find($id);
        $proposta->status = "Em análise";
        $proposta->sent_to_analysis_at = now();
        $proposta->save();

        return response()->json(['success' => true, 'status' => 200, 'id' => $id]);



    }


    public function recusar($id)
    {
        $proposta = Proposta::find($id);
        $proposta->status = "Recusada";
        $proposta->refused_at = now();
        $proposta->save();

        return response()->json(['success' => true, 'status' => 200, 'id' => $id]);
    }

    public function revisar($id)
    {
        $proposta = Proposta::find($id);
        $proposta->status = "Revisando";
        $proposta->save();

        return response()->json(['success' => true, 'status' => 200, 'id' => $id]);
    }

    public function aprovar($id, $s)
    {


        $proposta = Proposta::find($id);
        $proposta->status = "Aprovada";
        $proposta->approved_at = now();
        $proposta->save();

        $servicos = array();



        // //Criar os serviços automaticamente de acordo com a proposta

        if ($s == 1) {


            foreach ($proposta->servicos as $key => $s) {

                $servico = new Servico;
                $servico->nome = $s->servico;
                $servico->tipo = $s->servicoLpu->tipoServico;
                $servico->situacao = "andamento";
                $servico->responsavel_id = $s->responsavel_id;
                $servico->empresa_id = $proposta->empresa_id;
                $servico->unidade_id = $proposta->unidade_id;
                $servico->solicitante = $proposta->solicitante;
                $servico->escopo = $s->escopo;
                $servico->propostaServico_id = $s->id;
                $servico->proposta_id = $proposta->id;
                $servico->proposta = $proposta->id;


                if ($s->servicoPrincipal) {
                    $servicoPrincipal = Servico::where('propostaServico_id', $s->servicoPrincipal)->pluck('id')->first();
                    $servico->servicoPrincipal = $servicoPrincipal;
                }


                $servico->os = $this->getLastOs($proposta->unidade_id);

                $servico->save();
                $servicos[$key] = $servico;


                //Inserir financeiro


                $faturamento = new ServicoFinanceiro();
                $faturamento->servico_id = $servico->id;

                $faturamento->valorTotal = $s->valor;
                $faturamento->valorAberto = $s->valor;
                $faturamento->save();



                //Salvar historico


                $history = new Historico();
                $history->servico_id = $servico->id;
                $history->user_id = Auth::id();
                $history->observacoes = "Serviço " . $servico->id . " cadastrado.";
                $history->created_at = Carbon::now('america/sao_paulo');
                $history->save();


                //Criar Pendência principal

                $pendencia = new Pendencia;

                $pendencia->created_by = Auth::id();
                $pendencia->servico_id = $servico->id;
                $pendencia->pendencia = "Criar pendências!";
                $pendencia->vencimento = date('Y-m-d');
                $pendencia->prioridade = 1;


                $pendencia->responsavel_tipo = "usuario";
                $pendencia->responsavel_id = $s->responsavel_id;
                $pendencia->status = "pendente";
                $pendencia->observacoes = "Pendência criada automaticamente. Lembrar de criar pendências para esse serviço.";


                $pendencia->save();



            }



        }


        return response()->json(['success' => true, 'status' => 200, 'id' => $id, 'servicos' => $servicos]);




    }

    public function getLastOs($id)
    {
        // Retrieve unit based on id.
        $unit = Unidade::find($id);

        // Get company's full name.
        $fullName = $unit->empresa->razaoSocial;

        // Divide name into parts using whitespaces.
        $parts = explode(' ', $fullName);

        // Generate OS name by concatenating first letter of each part.
        $os = mb_substr($parts[0], 0, 1) . mb_substr($parts[1], 0, 1);

        // Get latest OS in which string starts with generated $os.
        $lastOS = Servico::where('os', 'like', '%' . $os . '%')->orderBy('os', 'DESC')->value('os');

        // If there's no such OS, return with default number.
        if (!$lastOS) {
            return $os . "0001";
        }

        // Otherwise, increase the archival number by 1.
        $number = (int) mb_substr($lastOS, 2) + 1;
        return $os . str_pad($number, 4, "0", STR_PAD_LEFT);
    }





    public function printPDF($id)
    {


        $proposta = Proposta::find($id);
        $this->reOrderServices($proposta->id); //Reordenar indice dos servicos da proposta


        $pdf = \PDF::loadView('admin.proposta.pdf', ['proposta' => $proposta])->stream("Proposta " . $proposta->id . " - " . $proposta->empresa->nomeFantasia . " - " . $proposta->unidade->codigo . " - " . $proposta->unidade->nomeFantasia . ".pdf");
        return $pdf;

        // return view('admin.proposta.pdf', ['proposta' => $proposta]);

    }

    public function reOrderServices($id)
    {
        $proposta = Proposta::find($id);

        foreach ($proposta->servicos->where('servicoPrincipal')->groupBy('servicoPrincipal') as $key => $s) {
            foreach ($s as $k => $c) {
                // dump($k);
                $serv = PropostaServico::find($c->id);
                $serv->posicao = $k + 1;
                $serv->save();
            }

        }


    }

    public function updateVendedor(Request $request)
    {
        $proposta = Proposta::find($request->proposta_id);
        if ($proposta) {
            $proposta->timestamps = false; // Do not update updated_at
            $proposta->created_by = $request->vendedor_id;
            $proposta->save();
            return response()->json(['success' => true, 'message' => 'Vendedor atualizado com sucesso.']);
        }
        return response()->json(['success' => false, 'message' => 'Proposta não encontrada.'], 404);
    }

    public function listData(Request $request)
    {
        $hoje = Carbon::now();
        $peri = $request->get('periodo', 'todos');
        $vendedorFiltro = $request->get('vendedor');
        $solicitanteFiltro = $request->get('solicitante');
        $statusFiltro = $request->get('status');

        $query = Proposta::select(['propostas.*'])
            ->addSelect([
                'valor_total' => \App\Models\PropostaServico::selectRaw('sum(valor)')
                    ->whereColumn('proposta_id', 'propostas.id')
            ])
            ->with([
                'empresa:id,nomeFantasia',
                'unidade:id,nomeFantasia,codigo',
                'vendedor:id,name',
            ])
            ->withCount(['servicosFaturados', 'servicosCriados'])
            ->whereNotIn('empresa_id', [16]);

        // Period Filtering
        if ($peri == 'mes_vigente') {
            $query->whereYear('propostas.created_at', $hoje->year)->whereMonth('propostas.created_at', $hoje->month);
        } elseif ($peri == 'mes_anterior') {
            $mesAnterior = $hoje->copy()->subMonth();
            $query->whereYear('propostas.created_at', $mesAnterior->year)->whereMonth('propostas.created_at', $mesAnterior->month);
        } elseif (strpos($peri, 'mes_') === 0) {
            $parts = explode('_', $peri);
            if (count($parts) == 3) {
                $query->whereYear('propostas.created_at', $parts[1])->whereMonth('propostas.created_at', $parts[2]);
            }
        } elseif ($peri == 'ano_atual') {
            $query->whereYear('propostas.created_at', $hoje->year);
        }

        // Status Filter
        if ($statusFiltro && $statusFiltro != 'Todos') {
            $query->where('propostas.status', $statusFiltro);
        }

        // User (Seller) Filter
        if ($vendedorFiltro) {
            $query->where('propostas.created_by', $vendedorFiltro);
        }

        // Requester Filter
        if ($solicitanteFiltro) {
            $query->where('propostas.solicitante', $solicitanteFiltro);
        }

        // Global Search
        if ($search = $request->input('search.value')) {
            $query->where(function ($q) use ($search) {
                $q->where('propostas.id', 'like', "%{$search}%")
                    ->orWhere('propostas.proposta', 'like', "%{$search}%")
                    ->orWhereHas('empresa', function ($sub) use ($search) {
                        $sub->where('nomeFantasia', 'like', "%{$search}%");
                    })
                    ->orWhereHas('unidade', function ($sub) use ($search) {
                        $sub->where('nomeFantasia', 'like', "%{$search}%")
                            ->orWhere('codigo', 'like', "%{$search}%");
                    })
                    ->orWhereHas('vendedor', function ($sub) use ($search) {
                        $sub->where('name', 'like', "%{$search}%");
                    });
            });
        }

        $recordsFiltered = (clone $query)->count();

        // Ordering
        // Mapping from Blade table structure: 
        // 0: ID, 1: Vendedor, 2: Cliente/Unidade, 3: Solicitante, 4: Total, 5: Status, 6: Dias em Análise, 7: Faturamento, 8: Actions
        $columns = [
            0 => 'propostas.id',
            1 => 'users.name',
            2 => 'empresas.nomeFantasia',
            3 => 'propostas.solicitante', // If it's IDs, we'd need another join
            4 => 'valor_total',
            5 => 'propostas.status',
            6 => 'propostas.updated_at',
            7 => 'propostas.created_at'
        ];

        $orderColumnIndex = $request->input('order.0.column', 0);
        $orderDir = $request->input('order.0.dir', 'desc');
        $orderField = $columns[$orderColumnIndex] ?? 'propostas.created_at';

        // Add joins only for ordering if needed
        if ($orderColumnIndex == 1) {
            $query->leftJoin('users', 'propostas.created_by', '=', 'users.id');
        } elseif ($orderColumnIndex == 2) {
            $query->leftJoin('empresas', 'propostas.empresa_id', '=', 'empresas.id');
        }

        $query->orderBy($orderField, $orderDir);

        // Pagination
        $start = $request->input('start', 0);
        $length = $request->input('length', 25);

        $propostas = $query->offset($start)->limit($length)->get();

        // Format data for response
        $data = $propostas->map(function ($p) {
            $dias = $p->dias_em_analise;
            $cor = 'green';
            if ($dias > 15)
                $cor = 'red';
            elseif ($dias >= 8)
                $cor = 'orange';

            // Resolve solicitante name if it's an ID
            $solicitanteName = 'N/A';
            if ($p->solicitante) {
                if (is_numeric($p->solicitante)) {
                    $sol = \App\Models\Solicitante::find($p->solicitante);
                    $solicitanteName = $sol->nome ?? 'N/A';
                } else {
                    $solicitanteName = $p->solicitante;
                }
            }

            $vendedor_nome = $p->vendedor->name ?? '';
            if ($vendedor_nome == 'Sistema')
                $vendedor_nome = '';

            return [
                'id' => $p->id,
                'proposta' => $p->proposta,
                'vendedor_nome' => $vendedor_nome,
                'empresa_nome' => $p->empresa->nomeFantasia ?? 'N/A',
                'unidade_nome' => $p->unidade->nomeFantasia ?? '',
                'unidade_codigo' => $p->unidade->codigo ?? '',
                'solicitante_nome' => $solicitanteName,
                'valor_total' => number_format($p->valorTotal(), 2, ',', '.'),
                'status' => $p->status,
                'dias_analise' => $dias,
                'dias_analise_cor' => $cor,
                'is_data_aproximada' => $p->is_data_aproximada,
                'approved_at' => ($p->status == 'Aprovada') ? \Carbon\Carbon::parse($p->approved_at ?: $p->updated_at)->format('d/m/Y') : null,
                'refused_at' => ($p->status == 'Recusada') ? \Carbon\Carbon::parse($p->refused_at ?: $p->updated_at)->format('d/m/Y') : null,
                'finalized_at' => in_array($p->status, ['Arquivada', 'Revisando']) ? \Carbon\Carbon::parse($p->updated_at)->format('d/m/Y') : null,
                'servicos_faturados_count' => $p->servicos_faturados_count,
                'servicos_criados_count' => $p->servicos_criados_count,
                'created_at' => $p->created_at->format('d/m/Y'),
                'edit_url' => route('proposta.edit', $p->id),
                'pdf_url' => route('propostaPDF', $p->id),
                'remove_url' => route('removerProposta', $p->id),
                'can_edit' => ($p->status == 'Revisando' || $p->status == 'Recusada'),
                'is_recusada' => $p->status == 'Recusada',
                'is_revisando' => $p->status == 'Revisando',
                'is_em_analise' => $p->status == 'Em análise',
                'is_arquivada' => $p->status == 'Arquivada',
            ];
        });

        return response()->json([
            'draw' => intval($request->input('draw')),
            'recordsTotal' => Proposta::whereNotIn('empresa_id', [16])->count(),
            'recordsFiltered' => $recordsFiltered,
            'data' => $data
        ]);
    }

}



