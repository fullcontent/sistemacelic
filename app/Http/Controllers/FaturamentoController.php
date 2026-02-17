<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\Empresa;
use App\Models\Servico;
use App\Models\Faturamento;
use Illuminate\Http\Request;
use App\Models\FaturamentoServico;
use App\Models\ServicoFinanceiro;
use App\Models\ServicoFinalizado;
use App\Models\DadosCastro;
use DB;




class FaturamentoController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $faturamentos = Faturamento::query()
            ->select('id', 'valorTotal', 'created_at', 'nf', 'nome', 'empresa_id', 'obs')
            ->with([
                'empresa' => function ($query) {
                    $query->select('id', 'nomeFantasia');
                },
                'servicos' => function ($query) {
                    $query->select('proposta');
                }
            ])
            ->orderBy('created_at', 'desc')
            ->get();


        //    return $faturamentos;
        //    dd($faturamentos);
        // $faturamentos = Faturamento::where('empresa_id',16)->get();

        return view('admin.faturamento.lista-faturamentos')->with([
            'faturamentos' => $faturamentos,
        ]);

    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {

        $empresas = Empresa::orderBy('nomeFantasia')->pluck('nomeFantasia', 'id');

        $propostas = [];


        return view('admin.faturamento.step1')->with(['empresas' => $empresas, 'propostas' => $propostas]);

    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */

    public function step2(Request $request)
    {
        $periodo = explode(' - ', $request->periodo);
        $start_date = Carbon::parse($periodo[0])->startOfDay()->toDateTimeString();
        $end_date = Carbon::parse($periodo[1])->endOfDay()->toDateTimeString();

        $empresas = Empresa::whereIn('id', $request->empresa_id)->get();

        $query = Servico::with('financeiro', 'faturado', 'servicoFinalizado', 'unidade');

        // Filter by company
        $query->whereHas('unidade', function ($q) use ($request) {
            $q->whereIn('empresa_id', $request->empresa_id);
        });

        // Filter by propostas
        if ($request->propostas) {
            $query->whereIn('proposta', $request->propostas);
        }

        // Filter by situations
        if ($request->situacoes) {
            $query->whereIn('situacao', $request->situacoes);
        } else {
            $query->whereIn('situacao', ['finalizado', 'arquivado']);
        }

        // Filter by date range (on finalization OR creation)
        $query->where(function ($q) use ($start_date, $end_date) {
            $q->whereHas('servicoFinalizado', function ($sq) use ($start_date, $end_date) {
                $sq->whereBetween('finalizado', [$start_date, $end_date]);
            })
                ->orWhereBetween('created_at', [$start_date, $end_date]);
        });

        // Filter by billing status
        $query->whereHas('financeiro', function ($q) use ($request) {
            $q->where(function ($sq) use ($request) {
                $anyFilter = false;
                if ($request->faturamento_100) {
                    $sq->orWhere('valorAberto', 0);
                    $anyFilter = true;
                }
                if ($request->faturamento_parcial) {
                    $sq->orWhere(function ($ssq) {
                        $ssq->where('valorAberto', '>', 0)
                            ->whereColumn('valorAberto', '<', 'valorTotal');
                    });
                    $anyFilter = true;
                }
                if ($request->faturamento_integral) {
                    $sq->orWhereColumn('valorAberto', 'valorTotal');
                    $anyFilter = true;
                }

                if (!$anyFilter) {
                    $sq->whereRaw('1 = 0');
                }
            });
        });

        $servicosFaturar = $query->get();

        return view('admin.faturamento.step2')->with([
            'servicosFaturar' => $servicosFaturar,
            'empresas' => $empresas,
            'periodo' => $periodo,
            'propostas' => $request->propostas,
        ]);
    }


    public function step3(Request $request)
    {



        $servicosFaturar = Servico::with('financeiro')
            ->whereIn('id', $request->servicos)
            ->get();

        $total = $servicosFaturar->sum('financeiro.valorAberto');

        $dadosCastro = DadosCastro::pluck('razaoSocial', 'id');



        $descricao = "00" . Carbon::now()->month . "-" . Carbon::now()->year . "";


        return view('admin.faturamento.step3')->with([
            'servicosFaturar' => $servicosFaturar,
            'total' => $total,
            'empresa_id' => $request->empresa_id,
            'descricao' => $descricao,
            'dadosCastro' => $dadosCastro,

        ]);


    }

    public function step4(Request $request)
    {




        //Criando faturamento

        $faturamento = new Faturamento;
        $faturamento->empresa_id = $request->empresa_id;
        $faturamento->link = $request->link;
        $faturamento->dadosCastro_id = $request->dadosCastro;
        $faturamento->save();


        //dump("Criou o faturamento ".$faturamento->id."");


        $servicos = [];

        //Selecionar os servicos based on servico_id of request

        // dump($request->all());


        foreach ($request->faturamento as $f) {
            $s = Servico::with('financeiro')->find($f['servico_id']);

            $s->nf = $f['nf'];
            $s->save();


            $valorFaturar = $f['valorFaturar'];


            //dump("Atualizando Financeiro");
            $this->atualizarFinanceiro($s->id, $valorFaturar);


            //dump("Inserindo item ".$s->id." no faturamento".$faturamento->id."");
            $this->salvarItemFaturamento($s->id, $faturamento->id, $valorFaturar);

            array_push($servicos, $s->id);


        }

        //======================================================



        $servicosFaturar = Servico::with('financeiro')
            ->whereIn('id', $servicos)
            ->get();


        $total = $servicosFaturar->sum('financeiro.valorFaturar');

        //dump("Atualizando Faturamento");        

        // $this->salvarFaturamento($total, $request->obs, $request->descricao, $request->empresa_id);
        $this->atualizarFaturamento($faturamento->id, $total, $request->obs, $request->descricao);


        return view('admin.faturamento.step4')->with([

            'faturamentoItens' => $servicosFaturar,
            'totalFaturamento' => $total,
            'descricao' => $request->descricao,
            'obs' => $request->obs,
            'link' => $request->link,
            'dadosCastro' => $faturamento->dadosCastro,

        ]);

    }


    public function faturarServicoSub(Request $request)
    {



        $servico = Servico::whereIn('id', $request->servicos)->first();



        if ($servico->servicoPrincipal != null) {
            // dump("esse servico é sub");
            $subServicos = Servico::where('servicoPrincipal', $servico->servicoPrincipal)
                ->orWhere('id', $servico->servicoPrincipal)
                ->pluck('id');

        } else {
            // dump("esse servico é principal");
            $subServicos = Servico::whereIn('id', $servico->subServicos->pluck('id'))
                ->pluck('id');
        }


        $servicosFaturar = Servico::with('financeiro')
            ->whereIn('id', $subServicos)
            ->orWhere('id', $servico->id)
            // ->whereHas('servicoFinalizado')
            ->get();






        $empresas = Empresa::where('id', $request->empresa_id)->get();


        // return $subServicos;


        return view('admin.faturamento.step2')->with([
            'servicosFaturar' => $servicosFaturar,
            'empresas' => $empresas,

        ]);

    }



    public function atualizarFinanceiro($servico_id, $valorFaturar)
    {




        $s = Servico::with('financeiro')->find($servico_id);



        if ($s->financeiro->valorFaturado == 0) {

            //dump("Esse servico NAO TEM FATURAMENTO NENHUM");

            //Se nao tiver nada faturado

            if ($valorFaturar == $s->financeiro->valorTotal) {
                //dump("Faturando COMPLETO e nao tem nada faturado ainda");
                //Para faturamento completo
                $s->financeiro()->update([
                    'valorAberto' => 0,
                    'valorFaturado' => $valorFaturar,
                    'status' => 'faturado',
                    'valorFaturar' => $valorFaturar,
                ]);

            } elseif ($valorFaturar < $s->financeiro->valorTotal) {


                //dump("Faturando Parcialmente e nao tem nada faturado ainda");
                //Para faturamento parcial
                $s->financeiro()->update([
                    'valorAberto' => $s->financeiro->valorAberto - $valorFaturar,
                    'valorFaturado' => $valorFaturar,
                    'valorFaturar' => $valorFaturar,
                    'status' => 'parcial',
                ]);

            }


        }




        if ($s->financeiro->valorFaturado > 0) {

            //Se já existir algum faturamento

            //dump("Esse servico ja tem algum faturamento");

            if ($valorFaturar + $s->financeiro->valorFaturado == $s->financeiro->valorTotal) {
                //dump("Faturando COMPLETO e ja tem algo faturado");
                //Para faturamento completo
                $s->financeiro()->update([
                    'valorAberto' => $s->financeiro->valorAberto - $valorFaturar,
                    'valorFaturado' => $s->financeiro->valorFaturado + $valorFaturar,
                    'valorFaturar' => $valorFaturar,
                    'status' => 'faturado',
                ]);
            } else {
                //Pra Faturamento parcial
                //dump("Faturando Parcialmente e ja tem faturas");        
                $s->financeiro()->update([
                    'valorAberto' => $s->financeiro->valorAberto - $valorFaturar,
                    'valorFaturado' => $s->financeiro->valorFaturado + $valorFaturar,
                    'valorFaturar' => $valorFaturar,
                    'status' => 'parcial',
                ]);


            }


        }




    }


    public function salvarFaturamento($total, $obs, $descricao, $empresa_id)
    {


        $faturamento = new Faturamento;
        $faturamento->nome = $descricao;
        $faturamento->obs = $obs;
        $faturamento->valorTotal = $total;
        $faturamento->empresa_id = $empresa_id; //TEST

        $faturamento->save();



    }


    public function atualizarFaturamento($faturamento_id, $total, $obs, $descricao)
    {
        $f = Faturamento::find($faturamento_id);


        $f->nome = $descricao;
        $f->obs = $obs;
        $f->valorTotal = $total;
        $f->save();
    }

    public function salvarItemFaturamento($servico, $faturamento_id, $valorFaturado)
    {


        $f2 = new FaturamentoServico;

        $f2->servico_id = $servico;
        $f2->faturamento_id = $faturamento_id;
        $f2->valorFaturado = $valorFaturado;
        $f2->save();


    }


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


        $faturamento = Faturamento::with('servicosFaturados.detalhes.unidade')->find($id);


        return view('admin.faturamento.detalhe-faturamento')->with([

            'faturamentoItens' => $faturamento->servicosFaturados,
            'totalFaturamento' => $faturamento->valorTotal,
            'descricao' => $faturamento->nome,
            'obs' => $faturamento->obs,
            'data' => $faturamento->created_at,
            'link' => $faturamento->link,
            'id' => $faturamento->id,
            'dadosCastro' => $faturamento->dadosCastro,
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
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request)
    {

        $faturamento = Faturamento::find($request->faturamentoID);

        $faturamento->nf = $request->nf;
        $faturamento->save();

        return redirect()->route('faturamentos.index');

    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {

        //Selecionando faturamento a ser destruido

        $faturamento = Faturamento::find($id);


        //Selecionando os servicos dentro desse faturamento


        $faturamentoServicos = FaturamentoServico::where('faturamento_id', $id)->get();



        foreach ($faturamentoServicos as $f) {

            $s = Servico::with('financeiro')->find($f->servico_id);


            //Contar se há mais faturamentos para esse serviço;
            $c = FaturamentoServico::where('servico_id', $f->servico_id)->count();


            if ($c > 1) {

                //dump("Servico faturado Parcialmente");


                if ($s->financeiro->status == 'parcial') {

                    //dump("Excluindo Servico faturado Parcialmente");



                    $s->financeiro()->update([

                        'valorAberto' => $s->financeiro->valorAberto + $f->valorFaturado,
                        'valorFaturado' => $s->financeiro->valorFaturado - $f->valorFaturado,
                        'valorFaturar' => 0,
                    ]);



                    $f->destroy($f->id);


                }

                if ($s->financeiro->status == 'faturado') {

                    //dump("Excluindo Servico Faturado parcialmente mas já foi feito o valor total");



                    $s->financeiro()->update([

                        'valorAberto' => $s->financeiro->valorAberto + $f->valorFaturado,
                        'valorFaturado' => $s->financeiro->valorFaturado - $f->valorFaturado,
                        'valorFaturar' => 0,

                    ]);



                    $f->destroy($f->id);


                }

            } else {

                //dump("Excluindo Servico faturado Totalmente");

                $s->financeiro()->update([

                    'valorAberto' => $s->financeiro->valorAberto + $f->valorFaturado,
                    'valorFaturado' => $s->financeiro->valorFaturado - $f->valorFaturado,
                    'valorFaturar' => 0,
                    'status' => 'aberto',
                ]);

                $f->destroy($f->id);

            }


        }

        //Excluindo faturamento

        // return "Excluindo Faturamento ".$faturamento->id."";

        $faturamento->destroy($faturamento->id);


        return $this->index();

    }

    public function addNF(Request $request)
    {

        $faturamento = Faturamento::find($request->faturamentoID);

        $faturamento->nf = $request->nf;
        $faturamento->save();

        return redirect()->route('faturamentos.index');

    }

    public function editarFaturamento(Request $request)
    {
        $faturamento = Faturamento::find($request->faturamentoID);



        $faturamento->nome = $request->nome;
        $faturamento->obs = $request->obs;
        $faturamento->save();

        return redirect()->route('faturamento.show', $request->faturamentoID);
    }


    static function formatCnpjCpf($value)
    {
        $cnpj_cpf = preg_replace("/\D/", '', $value);

        if (strlen($cnpj_cpf) === 11) {
            return preg_replace("/(\d{3})(\d{3})(\d{3})(\d{2})/", "\$1.\$2.\$3-\$4", $cnpj_cpf);
        }

        return preg_replace("/(\d{2})(\d{3})(\d{3})(\d{4})(\d{2})/", "\$1.\$2.\$3/\$4-\$5", $cnpj_cpf);
    }


    public function getAllServicesFinished()
    {

        $servicoFinalizado = Servico::whereHas('servicoFinalizado')->pluck('id');

        // dd($servicoFinalizado);




        $servicosFaturar = Servico::whereNotIn('id', $servicoFinalizado)->whereHas('finalizado')->get();


        // dd($servicosFaturar);

        foreach ($servicosFaturar as $s) {



            if (!ServicoFinalizado::where('servico_id', $s->id)->first()) {
                $servico = new ServicoFinalizado;
                $servico->servico_id = $s->id;
                $servico->finalizado = $s->finalizado->created_at;
                $servico->save();

                dump("Adicionado " . $s->id);
            } else {
                dump("JA FOI " . $s->id);
            }



        }





    }


    public function getErrors()
    {
        $servicos = ServicoFinanceiro::where('status', 'faturado')->get();

        foreach ($servicos as $s) {

            if ($s->valorAberto == $s->valorFaturado) {
                // $s->valorAberto = 0;
                // $s->valorFaturar = 0;
                // $s->save();

                dump($s->servico_id);
            }

        }


    }


    public function getPropostas($id)
    {
        $id = explode(',', $id);

        $empresas = \App\Models\Empresa::whereIn('id', $id)->whereHas('propostas')->with('propostas')->get();

        $propostas = [];
        foreach ($empresas as $i) {
            foreach ($i->propostas as $key => $j) {
                $propostas[$key] = $j['proposta'];
            }
        }

        $data = \App\Models\Servico::whereIn('proposta', $propostas)->distinct('proposta')->pluck('proposta');



        return response()->json(['data' => $data]);
    }
    public function printPDF($id)
    {
        $faturamento = Faturamento::with('servicosFaturados.detalhes.unidade', 'servicosFaturados.detalhes.financeiro', 'empresa', 'dadosCastro')->find($id);

        $filename = preg_replace('/[^A-Za-z0-9_\-]/', '_', $faturamento->nome) . '.pdf';

        // Base64 encoding for images to maximize speed and avoid permission/network issues
        $headerPath = public_path('img/headerCastro.png');
        $footerPath = public_path('img/footerCastro.png');

        $headerBase64 = '';
        $footerBase64 = '';

        if (file_exists($headerPath)) {
            $headerData = file_get_contents($headerPath);
            $headerBase64 = 'data:image/png;base64,' . base64_encode($headerData);
        }

        if (file_exists($footerPath)) {
            $footerData = file_get_contents($footerPath);
            $footerBase64 = 'data:image/png;base64,' . base64_encode($footerData);
        }

        $pdf = \PDF::loadView('admin.faturamento.pdf', [
            'faturamentoItens' => $faturamento->servicosFaturados,
            'faturamento' => $faturamento,
            'dadosCastro' => $faturamento->dadosCastro,
            'headerBase64' => $headerBase64,
            'footerBase64' => $footerBase64,
        ]);

        return $pdf->download($filename);
    }
}
