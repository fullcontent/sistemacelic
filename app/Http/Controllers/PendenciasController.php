<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Pendencia;
use App\Models\PendenciasVinculos;
use App\Models\Historico;
use App\Models\Servico;
use App\User;
use Auth;
use Carbon\Carbon;

class PendenciasController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        //
        
        $pendencias = Pendencia::where('servico_id',$request->servico_id)->get();
        $servico = Servico::find($request->servico_id);


        return view('admin.listar-pendencias')
                    ->with([
                        'pendencias'=>$pendencias,
                        'servico'=>$servico,
                    ]);
    }

    public function minhas()
    {
        
        $pendencias = Pendencia::where('responsavel_id',Auth::id())
                     ->where('status','pendente')
                    //  ->whereDoesntHave('vinculo')
                    ->get();
        
                    return view('admin.lista-pendencias')
                    ->with([
                        'pendencias'=>$pendencias,
                        'title'=>'Minhas pendências',
                    ]);

    }

    public function outras()
    {
        
        $servicos = Servico::where('responsavel_id',Auth::id())->pluck('id');
            
    		$pendencias = Pendencia::with('servico','unidade')
                            ->where('responsavel_id', '!=', Auth::id())
                            ->whereIn('servico_id',$servicos)
            				->get();

            $pendencias = $pendencias->where('status','pendente');
        	
                            return view('admin.lista-pendencias')
                            ->with([
                                'pendencias'=>$pendencias,
                                'title'=>'Outras pendências',
                            ]);

       
    }

    public function vinculadas()
    {
        
        $pendencias = Pendencia::where('responsavel_id',Auth::id())
                     ->where('status','pendente')
                     ->whereHas('vinculos')
                    ->get();
        
                    return view('admin.lista-pendencias')
                    ->with([
                        'pendencias'=>$pendencias,
                        'title'=>'Pendências Vinculadas',
                    ]);


    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create($servico_id)
    {
        if (!Auth::user()->permitir_interacoes) {
            return redirect()->back()->with('error', 'Você não tem permissão para criar pendências.');
        }
        //
        $s = Servico::find($servico_id);

        $servico = Servico::where('id',$servico_id)->pluck('os','id')->toArray();
        $responsaveis = User::orderBy('name')->where('active',1)->where('privileges','admin')->pluck('name','id')->toArray();

        $vinculo = Servico::where('unidade_id',$s->unidade->id)
                            ->where('situacao','andamento')
                            ->pluck('os','id')
                            ->toArray();
      
        $listaPendencias = self::getPendenciasDropdown();
        
        return view('admin.cadastro-pendencia')
                ->with([
                    'servico'=> $servico,
                    'servico_id'=>$servico_id,
                    'responsaveis'=>$responsaveis,
                    'vinculo'=>$vinculo,
                    'listaPendencias'=>$listaPendencias,
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
        if (!Auth::user()->permitir_interacoes) {
            return redirect()->back()->with('error', 'Você não tem permissão para criar pendências.');
        }

        $request->validate([
            'pendencia' => 'required',
            // 'vencimento'=>'required',
                       
        ]);
       

         
        $pendencia = new Pendencia;

        $pendencia->created_by = Auth::id();
        $pendencia->servico_id = $request->servico_id;
        $pendencia->pendencia  = $request->pendencia;
        
        if($request->vencimento){
            $pendencia->vencimento = Carbon::createFromFormat('d/m/Y', $request->vencimento)->toDateString(); 
        }

        if($request->dataLimite){
            $pendencia->dataLimite = Carbon::createFromFormat('d/m/Y', $request->dataLimite)->toDateString(); 
        }
        
        
        
        $pendencia->responsavel_tipo = $request->responsavel_tipo;
        $pendencia->responsavel_id = $request->responsavel_id;
        $pendencia->status = $request->status;
        $pendencia->observacoes = $request->observacoes;
        $pendencia->etapa = $request->etapa;
        // $pendencia->vinculo = $request->vinculo;


               
        $pendencia->save();

        if($request->vinculo)
        {
            foreach($request->vinculo as $v)
            {
                $vinculo = new PendenciasVinculos;
                $vinculo->servico_id = $v;
                $vinculo->pendencia_id = $pendencia->id;
                $vinculo->save();
            }
    
        }
        
        return redirect(route('servicos.show',$pendencia->servico_id));

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
        if (!Auth::user()->permitir_interacoes) {
            return redirect()->back()->with('error', 'Você não tem permissão para editar pendências.');
        }
        //
        

        $pendencia = Pendencia::find($id);

        $pendencia->vencimento = date('d/m/Y', strtotime($pendencia->vencimento));
        $pendencia->dataLimite = date('d/m/Y', strtotime($pendencia->dataLimite));


        $servico = Servico::where('id',$pendencia->servico_id)->pluck('os','id')->toArray();


        $vinculo = Servico::where('unidade_id',$pendencia->servico->unidade->id)
                            ->where('situacao','andamento')
                            ->pluck('os','id')
                            ->toArray();


        $responsaveis = User::orderBy('name')->where('active',1)->where('privileges','admin')->pluck('name','id')->toArray();
        
        $pendencias = Pendencia::where('servico_id',$pendencia->servico_id)->pluck('pendencia','id')->toArray();

        $vinculos = $pendencia->vinculos->pluck('os','id');

        $listaPendencias = self::getPendenciasDropdown($pendencia);
        
        return view('admin.editar-pendencia')->with(
            [
                'pendencia'=>$pendencia,
                'servico'=>$servico,
                'servico_id'=>$pendencia->servico_id,
                'pendencias' => $pendencias,
                'responsaveis'=>$responsaveis,
                'vinculo'=>$vinculo,
                'vinculos'=>$vinculos,
                'listaPendencias'=>$listaPendencias,
            ]
        );
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
        if (!Auth::user()->permitir_interacoes) {
            return redirect()->back()->with('error', 'Você não tem permissão para editar pendências.');
        }
        //

       $pendencia = Pendencia::find($id);

        $pendencia->created_by = Auth::id();
        $pendencia->servico_id = $request->servico_id;
        $pendencia->pendencia  = $request->pendencia;

        if($request->vencimento)
        {
            $pendencia->vencimento = Carbon::createFromFormat('d/m/Y', $request->vencimento)->toDateString();
        }
        
         if($request->dataLimite)
        {
            $pendencia->dataLimite = Carbon::createFromFormat('d/m/Y', $request->dataLimite)->toDateString();
        }


        $pendencia->responsavel_tipo = $request->responsavel_tipo;
        $pendencia->responsavel_id = $request->responsavel_id;
        $pendencia->status = $request->status;
        $pendencia->observacoes = $request->observacoes;
        $pendencia->vinculoPendencia = $request->vinculoPendencia;
        // $pendencia->vinculo = $request->vinculo;


        $pendencia->save();

        if($request->vinculo)
        {
            foreach($request->vinculo as $v)
            {
                $vinculo = new PendenciasVinculos;
                $vinculo->servico_id = $v;
                $vinculo->pendencia_id = $pendencia->id;
                $vinculo->save();
            }
        }
        

        //Save Interation

        if(!$pendencia->wasRecentlyCreated) {
            
            $changes = $pendencia->getChanges();
            unset($changes['updated_at']);


             foreach ($changes as $value => $key) {
                 
                    $history = new Historico();
                    $history->servico_id = $pendencia->servico_id;
                    $history->user_id = Auth::id();
                    $history->observacoes = 'Pendencia '.$pendencia->pendencia.' alterado '.$value.' para "'.$key.'"';
                    $history->created_at = Carbon::now();
                    $history->save();
             }
            }

        // return $pendencia;

        return redirect(route('servicos.show',$pendencia->servico_id));


    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function delete($id)
    {
        //
        $pendencia = Pendencia::destroy($id);
        return redirect()->back();
    }

    public function done($id)
    {
        
        $pendencia = Pendencia::find($id);
        $pendencia->status = 'concluido';
        $pendencia->save();

        $history = new Historico();
                    $history->servico_id = $pendencia->servico_id;
                    $history->user_id = Auth::id();
                    $history->observacoes = 'Concluiu a pendência '.$pendencia->pendencia.'.';
                    $history->created_at = Carbon::now();
                    $history->save();

        
    }

    public function undone($id)
    {
        
        $pendencia = Pendencia::find($id);
        $pendencia->status = 'pendente';
        $pendencia->save();

        
    }

    public function unPriority($id)
    {
        $pendencia = Pendencia::find($id);
        $pendencia->prioridade = 0;
        $pendencia->save();
    }

    public function priority($id)
    {
        $pendencia = Pendencia::find($id);
        $pendencia->prioridade = 1;
        $pendencia->save();

        $history = new Historico();
                    $history->servico_id = $pendencia->servico_id;
                    $history->user_id = Auth::id();
                    $history->observacoes = 'Marcou a pendência '.$pendencia->pendencia.' como prioridade.';
                    $history->created_at = Carbon::now();
                    $history->save();
    }
    
    public function removerVinculo($id,$servico_id)
    {
        $vinculo = PendenciasVinculos::where('servico_id',$servico_id)->where('pendencia_id',$id)->delete();
    
    }

    public static function getPendenciasDropdown($pendencia = null)
    {
        $path = storage_path('app/pendencias.json');
        $data = [];
        if (file_exists($path)) {
            $content = file_get_contents($path);
            $data = json_decode($content, true);
        }

        if (empty($data) || !is_array($data)) {
            $data = [
                'Responsabilidade Castro' => [
                    'Adequação em projeto',
                    'Comunicar cliente',
                    'Contato com órgão',
                    'Documental',
                    'Elaboração',
                    'Emissão de taxa',
                    'Montar processo',
                    'Pagamento de taxa',
                    'Pedido de prazo',
                    'Protocolar',
                    'Protocolar reentrada',
                    'RT',
                    'Tramitação interna'
                ],
                'Responsabilidade Cliente' => [
                    'Adequação física',
                    'Adequação em projeto',
                    'Documental',
                    'Em análise',
                    'Pagamento de taxa',
                    'Retorno cliente'
                ],
                'Responsabilidade Órgão' => [
                    'Em análise',
                    'Emissão de alvará',
                    'Retorno órgão'
                ],
                'Vinculada' => [
                    'Vinculada'
                ]
            ];
        }

        $options = [];
        foreach ($data as $category => $items) {
            $options[$category] = [];
            if (is_array($items)) {
                foreach ($items as $item) {
                    $options[$category][$item] = $item;
                }
            }
        }

        if ($pendencia) {
            $currentValue = is_object($pendencia) ? $pendencia->pendencia : $pendencia;
            $responsavelTipo = is_object($pendencia) ? $pendencia->responsavel_tipo : null;

            $categoryMap = [
                'usuario' => 'Responsabilidade Castro',
                'cliente' => 'Responsabilidade Cliente',
                'op' => 'Responsabilidade Órgão',
                'vinculada' => 'Vinculada'
            ];

            $categoryName = isset($categoryMap[$responsavelTipo]) ? $categoryMap[$responsavelTipo] : 'Responsabilidade Castro';

            if (!isset($options[$categoryName])) {
                $options[$categoryName] = [];
            }

            if (!isset($options[$categoryName][$currentValue])) {
                $options[$categoryName][$currentValue] = $currentValue;
            }
        }

        return $options;
    }

    public function dashboard(Request $request)
    {
        if (!Auth::check() || !Auth::user()->isCoordinatorOrAdmin()) {
            abort(403, 'Acesso não autorizado.');
        }

        $responsaveis = User::where('active', 1)->orderBy('name')->pluck('name', 'id')->toArray();
        $empresas = \App\Models\Empresa::orderBy('nomeFantasia')->pluck('nomeFantasia', 'id')->toArray();

        // Get filter values
        $responsavel_id = $request->get('responsavel_id');
        $status = $request->get('status', 'ativas'); // default to 'ativas' (pendente)
        $empresa_id = $request->get('empresa_id');
        $unidade_id = $request->get('unidade_id');
        $prioridade = $request->get('prioridade', 'todas');
        $data_inicio = $request->get('data_inicio');
        $data_fim = $request->get('data_fim');

        // Base query for listing
        $query = Pendencia::with(['servico.unidade.empresa', 'responsavel']);

        // Base query for counters (which includes all filters EXCEPT status)
        $counterQuery = Pendencia::query();

        // Helper closure to apply filters to both queries
        $applyFilters = function ($q) use ($responsavel_id, $empresa_id, $unidade_id, $prioridade, $data_inicio, $data_fim) {
            if ($responsavel_id) {
                $q->where('responsavel_id', $responsavel_id);
            }
            if ($empresa_id) {
                $q->whereHas('servico.unidade', function ($sq) use ($empresa_id) {
                    $sq->where('empresa_id', $empresa_id);
                });
            }
            if ($unidade_id) {
                $q->whereHas('servico', function ($sq) use ($unidade_id) {
                    $sq->where('unidade_id', $unidade_id);
                });
            }
            if ($prioridade !== 'todas') {
                $q->where('prioridade', $prioridade === 'sim' ? 1 : 0);
            }
            if ($data_inicio) {
                if (preg_match('/^\d{4}-\d{2}-\d{2}$/', $data_inicio)) {
                    $q->where('vencimento', '>=', $data_inicio);
                } else {
                    $q->where('vencimento', '>=', Carbon::createFromFormat('d/m/Y', $data_inicio)->toDateString());
                }
            }
            if ($data_fim) {
                if (preg_match('/^\d{4}-\d{2}-\d{2}$/', $data_fim)) {
                    $q->where('vencimento', '<=', $data_fim);
                } else {
                    $q->where('vencimento', '<=', Carbon::createFromFormat('d/m/Y', $data_fim)->toDateString());
                }
            }
        };

        // Apply filters to list query
        $applyFilters($query);

        // Apply status filter specifically to listing
        if ($status === 'ativas') {
            $query->where('status', 'pendente');
        } elseif ($status === 'atrasadas') {
            $query->where('status', 'pendente')->where('vencimento', '<', date('Y-m-d'));
        } elseif ($status === 'concluidas') {
            $query->where('status', 'concluido');
        }

        // Apply filters to counter query
        $applyFilters($counterQuery);

        // Calculate counters
        $totalPendencias = (clone $counterQuery)->count();
        $emAtraso = (clone $counterQuery)->where('status', 'pendente')->where('vencimento', '<', date('Y-m-d'))->count();
        $concluidas = (clone $counterQuery)->where('status', 'concluido')->count();

        // Paginate listings
        $pendencias = $query->orderBy('prioridade', 'desc')
                            ->orderBy('vencimento', 'asc')
                            ->paginate(50);

        // If unity is selected, retrieve it to populate select2 correctly on load
        $selectedUnidade = null;
        if ($unidade_id) {
            $selectedUnidade = \App\Models\Unidade::find($unidade_id);
        }

        return view('admin.dashboard-pendencias')->with([
            'pendencias' => $pendencias,
            'responsaveis' => $responsaveis,
            'empresas' => $empresas,
            'responsavel_id' => $responsavel_id,
            'status' => $status,
            'empresa_id' => $empresa_id,
            'unidade_id' => $unidade_id,
            'selectedUnidade' => $selectedUnidade,
            'prioridade' => $prioridade,
            'data_inicio' => $data_inicio,
            'data_fim' => $data_fim,
            'totalPendencias' => $totalPendencias,
            'emAtraso' => $emAtraso,
            'concluidas' => $concluidas,
            'title' => 'Dashboard de Pendências'
        ]);
    }
}
