<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\Empresa;
use App\Models\Servico;
use Illuminate\Http\Request;

class ReembolsoController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
      
        $empresas = Empresa::all()->pluck('nomeFantasia','id');

        return view('admin.reembolso.step1')->with(compact('empresas',$empresas));
        
    }

    public function step2(Request $request)
    {
        
        $periodo = explode(' - ', $request->periodo);
        $start_date = Carbon::parse($periodo[0])->toDateTimeString();
        $end_date = Carbon::parse($periodo[1])->toDateTimeString();

        
        $empresas = Empresa::whereIn('id',$request->empresa_id)->get();
        $s = array();
        $s2 = collect();
        $taxas = collect();
        
        foreach($empresas->pluck('id') as $e)
        {

            $empresa = Empresa::whereHas('servicosFaturar')->with('servicosFaturar')->find($e);
            $s = $empresa->servicosFaturar->pluck('id');
            $s2 = $s2->merge($s);
        }

        $servicosFaturar = Servico::whereIn('id', $s2)
                           
                            ->whereHas('reembolsos')
                            ->with('reembolsos')
                            ->whereHas('finalizado',function($q) use ($start_date, $end_date){
                                return $q->whereBetween('created_at', [$start_date,$end_date]);
                            })                     
                            ->get();

        
        foreach($servicosFaturar as $s)
        {

            foreach($s->reembolsos as $r)
            {

                $t = $r->id;
                $taxas = $taxas->merge($t);
            }


        }

        return $taxas;
        
        
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
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
