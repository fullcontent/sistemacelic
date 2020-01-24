<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Pendencia;
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

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create($servico_id)
    {
        //
        $servico = Servico::where('id',$servico_id)->pluck('os','id')->toArray();
        $responsaveis = User::pluck('name','id')->toArray();
      
        
        return view('admin.cadastro-pendencia')
                ->with([
                    'servico'=> $servico,
                    'responsaveis'=>$responsaveis,
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
        //

        $pendencia = new Pendencia;

        $pendencia->created_by = Auth::id();
        $pendencia->servico_id = $request->servico_id;
        $pendencia->pendencia  = $request->pendencia;
        $pendencia->vencimento = Carbon::createFromFormat('d/m/Y', $request->vencimento)->toDateString(); 
        $pendencia->responsavel_tipo = $request->responsavel_tipo;
        $pendencia->responsavel_id = $request->responsavel_id;
        $pendencia->status = $request->status;

        $pendencia->save();

        return redirect(route('pendencia.index',['servico_id'=>$pendencia->servico_id]));

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

        $responsaveis = User::pluck('name','id')->toArray();


        return view('admin.editar-pendencia')->with(
            [
                'pendencia'=>$pendencia,
                'servico'=>$servico,
                'responsaveis'=>$responsaveis,
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
        $pendencia->vencimento = Carbon::createFromFormat('d/m/Y', $request->vencimento)->toDateString(); 
        $pendencia->responsavel_tipo = $request->responsavel_tipo;
        $pendencia->responsavel_id = $request->responsavel_id;
        $pendencia->status = $request->status;


        $pendencia->save();

        // return $pendencia;

        return redirect(route('pendencia.index',['servico_id'=>$pendencia->servico_id]));


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
                    $history->save();

        
    }

    public function undone($id)
    {
        
        $pendencia = Pendencia::find($id);
        $pendencia->status = 'pendente';
        $pendencia->save();

        
    }
}
