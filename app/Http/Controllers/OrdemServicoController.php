<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Prestador;
use App\Models\Servico;
use App\Models\OrdemServico;
use App\Models\OrdemServicoPagamento;
use App\Models\OrdemServicoVinculo;

use Carbon\Carbon;
use Auth;

class OrdemServicoController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        $hoje = Carbon::now();
        
        // Count for stats
        $stats = [
            'total' => OrdemServico::count(),
            'mes_atual' => OrdemServico::whereYear('created_at', $hoje->year)->whereMonth('created_at', $hoje->month)->count(),
            'abertas' => OrdemServico::whereHas('situacaoPagamento')->count(),
            'pagas' => OrdemServico::whereDoesntHave('situacaoPagamento')->count(),
            'valor_total' => OrdemServico::sum('valorServico'),
        ];

        $prestadores = Prestador::orderBy('nome')->pluck('nome', 'id');
        $servicos = Servico::orderBy('os')->pluck('os', 'id');

        return view('admin.ordemServico.lista-ordemServicos')->with([
            'stats' => $stats,
            'prestadores' => $prestadores,
            'servicos' => $servicos
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\View\View|\Illuminate\Http\RedirectResponse
     */
    public function create($id = null)
    {   
        if ($id) {
            $servico = Servico::with(['unidade', 'empresa'])->find($id);
            if (!$servico) {
                return redirect()->back()->with('error', 'Serviço não encontrado.');
            }
            $servicos = [];
        } else {
            $servico = null;
            $servicos = Servico::with(['unidade', 'empresa'])->get()->mapWithKeys(function ($item) {
                return [$item->id => $item->unidade->nome . " - " . $item->nome . " (" . ($item->empresa ? $item->empresa->nomeFantasia : '---') . ")"];
            })->toArray();
        }

        $prestadores = Prestador::orderBy('nome')->pluck('nome', 'id')->toArray();

        return view('admin.ordemServico.cadastro-ordemServico')->with([
            'prestadores' => $prestadores,
            'servico' => $servico,
            'servicos' => $servicos,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {   


       
        // dd($request->all());

        // An empty array is created to hold the installment details.
        $parcela = [];
        
        // The loop iterates over the valorParcela array obtained from the request.
        foreach ($request->valorParcela as $v => $p) {
            // The value at a particular index is added as a key-value pair to the parcela array.
            $parcela[$v]['valorParcela'] = $p;
        }
        
        // The loop iterates over the dataVencimento array obtained from the request.
        foreach ($request->dataVencimento as $v => $p) {
            // The value at a particular index is added as a key-value pair to the parcela array.
            $parcela[$v]['dataVencimento'] = $p;
        }
        
        // The loop iterates over the dataPagamento array obtained from the request.
        foreach ($request->dataPagamento as $v => $p) {
            // The value at a particular index is added as a key-value pair to the parcela array.
            $parcela[$v]['dataPagamento'] = $p;
        }
        
        // Checks if comprovante exists in the request object. If it does, then the loop iterates over the comprovante array obtained from the request.
        if($request->comprovante)
        {
            foreach ($request->comprovante as $v => $p) {
                // The value at a particular index is added as a key-value pair to the parcela array.
                $parcela[$v]['comprovante'] = $p;
        }
        }
        
        
        
        // The loop iterates over the obs array obtained from the request.
        foreach ($request->obs as $v => $p) {
            // The value at a particular index is added as a key-value pair to the parcela array.
            $parcela[$v]['obs'] = $p;
        }
        

        $servicosVinculados = [];

        if($request->has('servicoVinculado_id') && is_array($request->servicoVinculado_id))
        {
            // The loop iterates over the servicoVinculado_id array obtained from the request.
            foreach ($request->servicoVinculado_id as $v => $p) {
                if ($p) {
                    $servicosVinculados[$v]['servicoVinculado_id'] = $p;
                    $servicosVinculados[$v]['servicoVinculado_nome'] = $request->servicoVinculado_nome[$v] ?? '---';
                    $servicosVinculados[$v]['servicoVinculado_valor'] = $request->servicoVinculado_valor[$v] ?? 0;
                    $servicosVinculados[$v]['servicoVinculado_reembolso'] = $request->servicoVinculado_reembolso[$v] ?? 'nao';
                }
            }
        }
        
        
        
                
        $ordemServico = new OrdemServico;
        $ordemServico->user_id = Auth::id();
        $ordemServico->prestador_id = $request->prestador_id;
        $ordemServico->valorServico = $request->valorServico;
        $ordemServico->escopo = $request->escopo;
        $ordemServico->servico_id = $request->servico_id;
        $ordemServico->formaPagamento = $request->formaPagamento;
        $ordemServico->situacao = $request->situacao;
        $ordemServico->save();


        if ($request->servicoPrincipal_id) {
            $ordemServicoServicoPrincipal = new OrdemServicoVinculo;
            $ordemServicoServicoPrincipal->ordemServico_id = $ordemServico->id;
            $ordemServicoServicoPrincipal->servico_id = $request->servicoPrincipal_id;
            $ordemServicoServicoPrincipal->valor = (float) str_replace(',', '.', $request->servicoPrincipal_valor);
            $ordemServicoServicoPrincipal->reembolso = $request->servicoPrincipal_reembolso;
            $ordemServicoServicoPrincipal->save();
        }


        

        foreach($servicosVinculados as $s => $ser)
        {   

            
            $ordemServicoServicoVinculado = new OrdemServicoVinculo;
            $ordemServicoServicoVinculado->ordemServico_id = $ordemServico->id;
            $ordemServicoServicoVinculado->servico_id = $ser['servicoVinculado_id'];
            $ordemServicoServicoVinculado->valor = $ser['servicoVinculado_valor'];
            $ordemServicoServicoVinculado->reembolso = $ser['servicoVinculado_reembolso'];
            $ordemServicoServicoVinculado->save();
        }




        
        // Loop through each element in the $parcela array and assign its values to a new OrdemServicoPagamento object
        foreach($parcela as $p => $par) 
        {
        
            // Create a new instance of the OrdemServicoPagamento model
            $ordemServicoPagamento = new OrdemServicoPagamento; 
        
            // Assign values to its properties based on data received
            $ordemServicoPagamento->ordemServico_id = $ordemServico->id; // ID of the related OrdemServico (parent object)
            $ordemServicoPagamento->formaPagamento = $ordemServico->formaPagamento; // Payment form selected for the OrdemServico
            $ordemServicoPagamento->parcela = $p+1; // Number of the payment installment being processed
            $ordemServicoPagamento->valor = $par['valorParcela']; // Value of the current payment installment
            
            // If there's a "dataVencimento" value set, use the Carbon library to convert it to a valid date format and set it as the value of the "dataVencimento" property
            if($par['dataVencimento']) 
            {
                $ordemServicoPagamento->dataVencimento = Carbon::createFromFormat('d/m/Y', $par['dataVencimento'])->toDateString(); 
            }
            
            // If there's a "dataPagamento" value set, use the Carbon library to convert it to a valid date format and set it as the value of the "dataPagamento" property
            if($par['dataPagamento'])
            {
                $ordemServicoPagamento->dataPagamento = Carbon::createFromFormat('d/m/Y', $par['dataPagamento'])->toDateString();
            }
            
            // Set the value of the "obs" property to the one received for the current installment
            $ordemServicoPagamento->obs = $par['obs'];
        
            // Set the value of the "comprovante" property to the one received for the current installment
            
           
            if(isset($par['comprovante']))
            {
                $ordemServicoPagamento->comprovante = $par['comprovante'];

                //Se informou o arquivo, retorna um boolean
                if ($ordemServicoPagamento->comprovante->isValid()) {
                    $nameFile = null;
                    $name = uniqid(date('HisYmd'));
                    $extension = $ordemServicoPagamento->comprovante->extension();
                    $nameFile = "{$name}.{$extension}";
                    //Faz o upload:
                    $upload = $ordemServicoPagamento->comprovante->storeAs('comprovantes', $nameFile);
                    //Se tiver funcionado o arquivo foi armazenado em storage/app/public/categories/nomedinamicoarquivo.extensao
                
                    $ordemServicoPagamento->comprovante = $upload;
                }
            }
            
           
            

        
            // If a payment has been made (there's a comprovante file attached), set the value of the "situacao" property to 'pago'. Otherwise, set it to 'aberto'
            if($ordemServicoPagamento->comprovante)
            {
                $ordemServicoPagamento->situacao = 'pago';
            }
            else
            {
                $ordemServicoPagamento->situacao = 'aberto';
            }
            
            // Save the current OrdemServicoPagamento object to the database

            // dump($ordemServicoPagamento);

            $ordemServicoPagamento->save();
        }



        if ($request->servico_id) {
            return redirect()->route('servicos.show', $request->servico_id)->with('message', 'Ordem de serviço criada com sucesso!');
        }

        return redirect()->route('ordemServico.index')->with('message', 'Ordem de serviço criada com sucesso!');



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
        

        $ordemServico = OrdemServico::with(['pagamentos', 'vinculos.servico', 'prestador'])->find($id);
        
        if (!$ordemServico) {
            return redirect()->back()->with('error', 'Ordem de serviço não encontrada.');
        }

        $servico = Servico::with(['unidade', 'empresa'])->find($ordemServico->servico_id);
        $prestadores = Prestador::orderBy('nome')->pluck('nome','id')->toArray();

        $servicos = Servico::with(['unidade', 'empresa'])->get()->mapWithKeys(function ($item) {
            return [$item->id => $item->unidade->nome . " - " . $item->nome . " (" . ($item->empresa ? $item->empresa->nomeFantasia : '---') . ")"];
        })->toArray();

        // Encontrar o vínculo que corresponde ao serviço principal
        $vinculoPrincipal = $ordemServico->vinculos->where('servico_id', $ordemServico->servico_id)->first();
        $vinculoOutros = $ordemServico->vinculos->where('servico_id', '!=', $ordemServico->servico_id);

        return view('admin.ordemServico.editar-ordemServico')->with([
            'ordemServico'=>$ordemServico,
            'servico'=>$servico,
            'servicos' => $servicos,
            'prestadores'=>$prestadores,
            'vinculoPrincipal'=>$vinculoPrincipal,
            'vinculoOutros'=>$vinculoOutros
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
        // Validação da soma dos valores vinculados
        $valorTotal = (float) str_replace(',', '.', $request->valorServico);
        $valorPrincipal = (float) str_replace(',', '.', $request->servicoPrincipal_valor);
        $somaVinculos = 0;
        
        if ($request->servicoVinculado_valor) {
            foreach ($request->servicoVinculado_valor as $v) {
                $somaVinculos += (float) str_replace(',', '.', $v);
            }
        }

        if (($valorPrincipal + $somaVinculos) > ($valorTotal + 0.01)) {
            return redirect()->back()->withInput()->with('error', 'A soma dos valores vinculados (R$ '.number_format($valorPrincipal + $somaVinculos, 2, ',', '.').') excede o valor total da OS (R$ '.number_format($valorTotal, 2, ',', '.').').');
        }

        $ordemServico = OrdemServico::find($id);
        
        if (!$ordemServico) {
            return redirect()->back()->with('error', 'Ordem de serviço não encontrada.');
        }

        $ordemServico->user_id = Auth::id();
        $ordemServico->prestador_id = $request->prestador_id;
        $ordemServico->valorServico = $request->valorServico;
        $ordemServico->escopo = $request->escopo;
        $ordemServico->formaPagamento = $request->formaPagamento;
        $ordemServico->situacao = $request->situacao ?? $ordemServico->situacao;
        $ordemServico->save();

        // Atualizar vínculos
        OrdemServicoVinculo::where('ordemServico_id', $id)->delete();

        // Principal
        if ($request->servicoPrincipal_id) {
            $ordemServicoServicoPrincipal = new OrdemServicoVinculo;
            $ordemServicoServicoPrincipal->ordemServico_id = $id;
            $ordemServicoServicoPrincipal->servico_id = $request->servicoPrincipal_id;
            $ordemServicoServicoPrincipal->valor = (float) str_replace(',', '.', $request->servicoPrincipal_valor);
            $ordemServicoServicoPrincipal->reembolso = $request->servicoPrincipal_reembolso;
            $ordemServicoServicoPrincipal->save();
        }

        // Outros
        if($request->has('servicoVinculado_id') && is_array($request->servicoVinculado_id))
        {
            foreach ($request->servicoVinculado_id as $v => $p) {
                if ($p) {
                    $ocv = new OrdemServicoVinculo;
                    $ocv->ordemServico_id = $id;
                    $ocv->servico_id = $p;
                    $ocv->valor = $request->servicoVinculado_valor[$v] ?? 0;
                    $ocv->reembolso = $request->servicoVinculado_reembolso[$v] ?? 'nao';
                    $ocv->save();
                }
            }
        }

        // Atualizar pagamentos (simplificado: remove e recria se mudar, ou apenas permite atualizar se for o mesmo número de parcelas)
        // Para manter a simplicidade e consistência com o store:
        OrdemServicoPagamento::where('ordemServico_id', $id)->delete();
        
        if($request->valorParcela) {
            foreach($request->valorParcela as $p => $vParcela) {
                $pag = new OrdemServicoPagamento;
                $pag->ordemServico_id = $id;
                $pag->formaPagamento = $ordemServico->formaPagamento;
                $pag->parcela = $p + 1;
                $pag->valor = $vParcela;
                
                if(isset($request->dataVencimento[$p]) && $request->dataVencimento[$p]) {
                    $pag->dataVencimento = Carbon::createFromFormat('d/m/Y', $request->dataVencimento[$p])->toDateString();
                }
                
                if(isset($request->dataPagamento[$p]) && $request->dataPagamento[$p]) {
                    $pag->dataPagamento = Carbon::createFromFormat('d/m/Y', $request->dataPagamento[$p])->toDateString();
                }
                
                $pag->obs = $request->obs[$p] ?? null;

                // Lógica de Comprovante
                if($request->hasFile("comprovante.$p"))
                {
                    $file = $request->file("comprovante.$p");
                    if ($file->isValid()) {
                        $name = uniqid(date('HisYmd'));
                        $extension = $file->extension();
                        $nameFile = "{$name}.{$extension}";
                        $upload = $file->storeAs('comprovantes', $nameFile);
                        $pag->comprovante = $upload;
                    }
                }
                elseif(isset($request->comprovante_atual[$p]))
                {
                    // Mantém o arquivo antigo se não subiu um novo
                    $pag->comprovante = $request->comprovante_atual[$p];
                }

                $pag->situacao = $pag->comprovante ? 'pago' : 'aberto';
                $pag->save();
            }
        }

        if ($ordemServico->servico_id) {
            return redirect()->route('servicos.show', $ordemServico->servico_id)->with('message', 'Ordem de serviço atualizada com sucesso!');
        }

        return redirect()->route('ordemServico.index')->with('message', 'Ordem de serviço atualizada com sucesso!');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $ordemServico = OrdemServico::find($id);
        
        if (!$ordemServico) {
            return redirect()->back()->with('error', 'Ordem de serviço não encontrada.');
        }

        $servico_id = $ordemServico->servico_id;

        // Deletar dependências manually se não tiver cascade no DB
        OrdemServicoPagamento::where('ordemServico_id', $id)->delete();
        OrdemServicoVinculo::where('ordemServico_id', $id)->delete();
        
        $ordemServico->delete();

    }

    private function calculateMedian($queryOrCollection, $column)
    {
        $values = ($queryOrCollection instanceof \Illuminate\Database\Eloquent\Builder) 
            ? $queryOrCollection->pluck($column) 
            : $queryOrCollection->pluck($column);
            
        $values = $values->sort()->values();
        $count = $values->count();
        
        if ($count === 0) return 0;
        
        $middle = floor($count / 2);
        
        if ($count % 2 === 0) {
            return ($values[$middle - 1] + $values[$middle]) / 2;
        }
        
        return $values[$middle];
    }

    public function listData(Request $request)
    {
        $query = OrdemServico::with(['prestador', 'servicoPrincipal', 'pagamentos', 'rating']);

        // Filters
        if ($prestador_id = $request->get('prestador_id')) {
            $query->where('prestador_id', $prestador_id);
        }

        if ($situacao = $request->get('situacao')) {
            if ($situacao == 'aberto') {
                $query->whereHas('situacaoPagamento');
            } else {
                $query->whereDoesntHave('situacaoPagamento');
            }
        }

        // Global Search
        if ($search = $request->input('search.value')) {
            $query->where(function ($q) use ($search) {
                $q->where('id', 'like', "%{$search}%")
                    ->orWhere('escopo', 'like', "%{$search}%")
                    ->orWhereHas('prestador', function ($sub) use ($search) {
                        $sub->where('nome', 'like', "%{$search}%");
                    })
                    ->orWhereHas('servicoPrincipal', function ($sub) use ($search) {
                        $sub->where('os', 'like', "%{$search}%")
                            ->orWhere('nome', 'like', "%{$search}%");
                    });
            });
        }

        $recordsFiltered = $query->count();

        // Ordering
        $columns = [
            0 => 'id',
            1 => 'prestador_id',
            2 => 'servico_id',
            4 => 'valorServico',
            7 => 'created_at'
        ];
        
        $orderColumnIndex = $request->input('order.0.column', 0);
        $orderDir = $request->input('order.0.dir', 'desc');
        $orderField = $columns[$orderColumnIndex] ?? 'created_at';
        $query->orderBy($orderField, $orderDir);

        // Pagination
        $start = $request->input('start', 0);
        $length = $request->input('length', 25);
        $ordens = $query->offset($start)->limit($length)->get();

        $data = $ordens->map(function($oc) {
            $has_open = $oc->situacaoPagamento->count() > 0;
            $paid_count = $oc->pagamentos->where('situacao', 'pago')->count();
            $total_count = $oc->pagamentos->count();
            
            // Clean HTML from scope
            $clean_escopo = strip_tags($oc->escopo);
            if (strlen($clean_escopo) > 80) {
                $clean_escopo = substr($clean_escopo, 0, 80) . '...';
            }

            // Rating logic: Use Median for individual OS and Overview
            $all_prestador_ratings = \App\Models\PrestadorComentario::where('prestador_id', $oc->prestador_id);
            $total_prestador_ratings = (clone $all_prestador_ratings)->count();
            $median_overall = $this->calculateMedian($all_prestador_ratings, 'rating');
            
            $os_median = $this->calculateMedian($oc->rating, 'rating');

            return [
                'id' => $oc->id,
                'prestador_nome' => optional($oc->prestador)->nome ?? 'N/A',
                'servico_os' => optional($oc->servicoPrincipal)->os ?? 'N/A',
                'servico_nome' => optional($oc->servicoPrincipal)->nome ?? 'Serviço não vinculado',
                'servico_show_url' => $oc->servico_id ? route('servicos.show', $oc->servico_id) : null,
                'escopo' => $clean_escopo,
                'valor' => 'R$ ' . number_format($oc->valorServico, 2, ',', '.'),
                'formaPagamento' => ($oc->formaPagamento == 1) ? 'à vista' : $oc->formaPagamento . 'x',
                'situacao_html' => $has_open 
                    ? '<span class="label label-warning" style="font-size: 11px;">Em aberto ('.$paid_count.'/'.$total_count.')</span>'
                    : '<span class="label label-success" style="font-size: 11px;">Pago</span>',
                'rating_html' => ($oc->rating->count() > 0 
                    ? '<div class="Stars" style="--rating: '.$os_median.';"></div>'
                    : '<button class="btn btn-xs btn-info rate-btn" data-id="'.$oc->id.'" data-prestador="'.$oc->prestador_id.'" style="border-radius: 50px; padding: 2px 10px;">Avaliar</button>') . 
                    ($total_prestador_ratings > 0 
                        ? '<div style="margin-top: 5px;"><span class="label label-default" title="Mediana: '.number_format($median_overall, 1).'" style="background-color: #f4f4f4; color: #777; border: 1px solid #ddd; font-weight: normal;"><i class="fa fa-star text-yellow"></i> '.$total_prestador_ratings.' avaliações totais</span></div>'
                        : ''),
                'prestador_info' => '',
                'edit_url' => route('ordemServico.edit', $oc->id),
                'delete_url' => route('ordemServico.destroy', $oc->id),
                'view_ratings_btn' => $oc->rating->count() > 0 
                    ? '<div style="margin-top: 4px;"><a href="#" class="btn btn-xs btn-default rates-show-btn" data-id="'.$oc->id.'" data-prestador="'.$oc->prestador_id.'" style="border-radius: 50px;">ver avaliações ('.$oc->rating->count().')</a></div>'
                    : '',
                'acoes' => '<div class="btn-group" style="display: flex; gap: 5px; justify-content: center;">
                                <a href="'.route('ordemServico.edit', $oc->id).'" class="btn btn-xs btn-default" title="Editar"><i class="fa fa-edit text-blue"></i></a>
                                <button type="button" class="btn btn-xs btn-default delete-btn" data-id="'.$oc->id.'" data-url="'.route('ordemServico.destroy', $oc->id).'" title="Excluir"><i class="fa fa-trash text-red"></i></button>
                            </div>'
            ];
        });

        return response()->json([
            'draw' => intval($request->input('draw')),
            'recordsTotal' => OrdemServico::count(),
            'recordsFiltered' => $recordsFiltered,
            'data' => $data
        ]);
    }
}
