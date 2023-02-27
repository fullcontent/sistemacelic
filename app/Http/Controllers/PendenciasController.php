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
        //
        $s = Servico::find($servico_id);

        $servico = Servico::where('id',$servico_id)->pluck('os','id')->toArray();
        $responsaveis = User::orderBy('name')->where('active',1)->pluck('name','id')->toArray();

        $vinculo = Servico::where('unidade_id',$s->unidade->id)
                            ->where('situacao','andamento')
                            ->pluck('os','id')
                            ->toArray();
      
        
        return view('admin.cadastro-pendencia')
                ->with([
                    'servico'=> $servico,
                    'servico_id'=>$servico_id,
                    'responsaveis'=>$responsaveis,
                    'vinculo'=>$vinculo,
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
        //
        

        $pendencia = Pendencia::find($id);

        $pendencia->vencimento = date('d/m/Y', strtotime($pendencia->vencimento));

        $servico = Servico::where('id',$pendencia->servico_id)->pluck('os','id')->toArray();


        $vinculo = Servico::where('unidade_id',$pendencia->servico->unidade->id)
                            ->where('situacao','andamento')
                            ->pluck('os','id')
                            ->toArray();


        $responsaveis = User::orderBy('name')->where('active',1)->pluck('name','id')->toArray();
        
        $pendencias = Pendencia::where('servico_id',$pendencia->servico_id)->pluck('pendencia','id')->toArray();

        $vinculos = $pendencia->vinculos->pluck('os','id');

        
        
        return view('admin.editar-pendencia')->with(
            [
                'pendencia'=>$pendencia,
                'servico'=>$servico,
                'servico_id'=>$pendencia->servico_id,
                'pendencias' => $pendencias,
                'responsaveis'=>$responsaveis,
                'vinculo'=>$vinculo,
                'vinculos'=>$vinculos,
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
        //

       $pendencia = Pendencia::find($id);

        $pendencia->created_by = Auth::id();
        $pendencia->servico_id = $request->servico_id;
        $pendencia->pendencia  = $request->pendencia;

        if($request->vencimento)
        {
            $pendencia->vencimento = Carbon::createFromFormat('d/m/Y', $request->vencimento)->toDateString();
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
                    $history->created_at = Carbon::now('america/sao_paulo');
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
                    $history->created_at = Carbon::now('america/sao_paulo');
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
                    $history->created_at = Carbon::now('america/sao_paulo');
                    $history->save();
    }
    
    public function removerVinculo($id,$servico_id)
    {
        $vinculo = PendenciasVinculos::where('servico_id',$servico_id)->where('pendencia_id',$id)->delete();
    
    }
}
