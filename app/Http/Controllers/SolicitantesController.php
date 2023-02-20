<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Solicitante;
use App\Models\SolicitanteEmpresa;
use App\Models\Empresa;


class SolicitantesController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $solicitantes = Solicitante::with('empresas')->get();

       

        return view('admin.solicitantes.lista')->with('solicitantes',$solicitantes);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $empresas = Empresa::orderBy('nomeFantasia')->pluck('nomeFantasia','id');



        return view('admin.solicitantes.cadastrar')->with('empresas',$empresas);
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

        $solicitante = new Solicitante;

        $solicitante->nome = $request->nome;
        $solicitante->email = $request->email;
        $solicitante->telefone = $request->telefone;
        $solicitante->departamento = $request->departamento;
        $solicitante->save();
        
        foreach($request->empresas as $s)
        {
            $solicEmp = new SolicitanteEmpresa;
            $solicEmp->empresa_id = $s;
            $solicEmp->solicitante_id = $solicitante->id;
            $solicEmp->save();
           

        }
        

        

        // return $request->all();

        return redirect()->route('solicitantes.index');


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
        $solicitante = Solicitante::find($id);
        $empresas = Empresa::orderBy('nomeFantasia')->pluck('nomeFantasia','id');
        
        
        return view('admin.solicitantes.editar')->with(['empresas'=>$empresas,'solicitante'=>$solicitante]);
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
        $solicitante = Solicitante::find($id);


        
            $solicitante->nome = $request->nome;
            $solicitante->email = $request->email;
            $solicitante->telefone = $request->telefone;
            $solicitante->departamento = $request->departamento;

            $solicitante->save();
     
        
        $oldEmpresas = $solicitante->empresas->pluck('id')->toArray();
        $newEmpresas = $request->empresas;

        if($newEmpresas)
                {
                    if($oldEmpresas != $newEmpresas)
                    {   
                    //    dump("diferente");
                                                                     
                       if($oldEmpresas < $newEmpresas)
                       {
                            // dump("adicionou");
                                        
                            $nE = array_diff($newEmpresas,$oldEmpresas);
                            
                            foreach($nE as $n)
                            {
                                $ss = new SolicitanteEmpresa();
                                $ss->empresa_id = $n;
                                $ss->solicitante_id = $solicitante->id;
                                $ss->save();
                            }
            
            
                       }
                       if($oldEmpresas > $newEmpresas)
                       {
                            // dump("removeu");
                            
                                        
                            $nE = array_diff($oldEmpresas,$newEmpresas);
                            foreach($nE as $n)
                            {
                               $ss = SolicitanteEmpresa::where('solicitante_id',$solicitante->id)->where('empresa_id',$n)->delete();
                            }
                       }
                       
                    }
                }
            else{

                // dump("removeu Tudo");

                foreach($solicitante->empresas as $e){
                    $ss = SolicitanteEmpresa::where('solicitante_id',$solicitante->id)->where('empresa_id',$e->id)->delete();
                }
            }


        return redirect()->route('solicitantes.index');

    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {   
        
        $solicitante = Solicitante::find($id);
        $solicitante->destroy($id);

        return redirect()->route('solicitantes.index');
    }
    
}
