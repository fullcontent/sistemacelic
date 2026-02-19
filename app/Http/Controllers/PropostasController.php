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

        $query = Proposta::select(['id', 'proposta', 'empresa_id', 'unidade_id', 'status', 'created_at'])
            ->addSelect([
                'valor_total' => \App\Models\PropostaServico::selectRaw('sum(valor)')
                    ->whereColumn('proposta_id', 'propostas.id')
            ])
            ->with([
                'empresa' => function ($q) {
                    $q->select('id', 'nomeFantasia');
                },
                'unidade' => function ($q) {
                    $q->select('id', 'nomeFantasia', 'codigo');
                }
            ])
            ->withCount(['servicosFaturados', 'servicosCriados'])
            ->whereNotIn('empresa_id', [16]);

        // Period Filtering
        if ($periodo == 'mes_vigente') {
            $query->whereYear('created_at', $hoje->year)->whereMonth('created_at', $hoje->month);
        } elseif ($periodo == 'mes_anterior') {
            $mesAnterior = $hoje->copy()->subMonth();
            $query->whereYear('created_at', $mesAnterior->year)->whereMonth('created_at', $mesAnterior->month);
        } elseif ($periodo == 'ano_atual') {
            $query->whereYear('created_at', $hoje->year);
        }

        if ($statusFiltro) {
            $query->where('status', $statusFiltro);
        }

        $propostas = $query->orderBy('created_at', 'DESC')
            ->paginate(50);

        // Base query for stats with standard exclusions
        $baseStatsQuery = Proposta::whereNotIn('empresa_id', [16])->where('status', '!=', 'Arquivada');

        // Apply period filtering to stats queries
        if ($periodo == 'mes_vigente') {
            $baseStatsQuery->whereYear('created_at', $hoje->year)->whereMonth('created_at', $hoje->month);
        } elseif ($periodo == 'mes_anterior') {
            $mesAnterior = $hoje->copy()->subMonth();
            $baseStatsQuery->whereYear('created_at', $mesAnterior->year)->whereMonth('created_at', $mesAnterior->month);
        } elseif ($periodo == 'ano_atual') {
            $baseStatsQuery->whereYear('created_at', $hoje->year);
        }

        $stats = [];
        $stats['elaboracao_count'] = (clone $baseStatsQuery)->where('status', 'Revisando')->count();
        $stats['analise_count'] = (clone $baseStatsQuery)->where('status', 'Em análise')->count();
        $stats['aprovadas_mes_count'] = (clone $baseStatsQuery)->where('status', 'Aprovada')->count();
        $stats['recusadas_count'] = (clone $baseStatsQuery)->where('status', 'Recusada')->count();

        $totalNoPeriodo = (clone $baseStatsQuery)->count();
        $totalAprovadasNoPeriodo = $stats['aprovadas_mes_count'];
        $stats['conversao'] = $totalNoPeriodo > 0 ? ($totalAprovadasNoPeriodo / $totalNoPeriodo) * 100 : 0;

        $stats['status_atual'] = $statusFiltro;
        $stats['periodo_atual'] = $periodo;

        return view('admin.proposta.lista-propostas')
            ->with('propostas', $propostas)
            ->with('stats', $stats);
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

    public function step3(Type $var = null)
    {
        # code...
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
        $proposta->save();

        return response()->json(['success' => true, 'status' => 200, 'id' => $id]);



    }


    public function recusar($id)
    {
        $proposta = Proposta::find($id);
        $proposta->status = "Recusada";
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
        $os = substr($parts[0], 0, 1) . substr($parts[1], 0, 1);

        // Get latest OS in which string starts with generated $os.
        $lastOS = Servico::where('os', 'like', '%' . $os . '%')->orderBy('os', 'DESC')->value('os');

        // If there's no such OS, return with default number.
        if (!$lastOS) {
            return $os . "0001";
        }

        // Otherwise, increase the archival number by 1.
        $number = (int) substr($lastOS, 2) + 1;
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

}



