<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Auth;
use DB;
use App\User;
use App\Models\Servico;
use App\Models\Pendencia;
use App\UserAccess;
use App\Models\Unidade;
use App\Models\Empresa;


use App\Notifications\TestNotification;


class AdminController extends Controller
{
    //


	public function __construct()
    {
        
        $this->middleware('admin');       

    }

    public function index()
    {
    			
		
		
        // return $this->pendencias();
		
		return view('admin.dashboard')
					->with([
						'vencer'=>$this->servicosVencer(),
						'finalizados'=>$this->servicosFinalizados(),
						'andamento'=>$this->servicosAndamento(),
						'pendencias'=>$this->pendencias(),
						
					]);
    }

    

    public function pendencias()
    {	

    	
    		$servicos = Servico::select('id')->where('responsavel_id',Auth::id())->get();
            
    		$pendencias = Pendencia::with('servico','unidade')
                            ->where('responsavel_id', Auth::id())
                            // ->orWhereIn('pendencias.servico_id',$servicos)
                            ->where('status','pendente')
                            ->whereDoesntHave('vinculo')
            				->get();
           
        	
        	return $pendencias;
    }

    public function servicosVencer()
    {
                    $servicos = Servico::with('unidade','empresa','responsavel')
                    // ->whereIn('unidade_id',$this->getUnidadesList())
                    ->orWhere('responsavel_id',Auth::id())
                    ->get();

                    $servicos = $servicos->where('licenca_validade','<',\Carbon\Carbon::today()->addDays(60))
                        
                        ->where('situacao','=','finalizado') 
                        ->where('tipo','=','licencaOperacao');    

                    return $servicos;
                }

    public function servicosFinalizados()
    {
        $servicos = Servico::with('unidade','empresa','responsavel')
        						
        // ->whereIn('unidade_id',$this->getUnidadesList())
        ->orWhere('responsavel_id',Auth::id())
        ->get();


$servicos = $servicos->where('situacao','=','finalizado')
        
        ->where('situacao','<>','arquivado');

         return $servicos;
    }

    public function servicosAndamento()
    {
        $servicos = Servico::with('unidade','empresa','responsavel')
                                
        // ->whereIn('unidade_id',$this->getUnidadesList())
        ->orWhere('responsavel_id',Auth::id())
        ->get();


$servicos = $servicos->where('situacao','=','andamento')
        
        ->where('situacao','<>','arquivado');

        return $servicos;
    }


    public function relatorioCompleto()
    {
        
        $servicos = Servico::with('unidade','responsavel','financeiro')->whereNotIn('responsavel_id',[1])->get();

        // dump($servicos);

        return view('admin.relatorios.completo')->with(['servicos'=>$servicos]);


    }


}
