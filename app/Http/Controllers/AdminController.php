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

    	
    		$servicos = Servico::where('responsavel_id',Auth::id())->pluck('id');
    		$pendencias = Pendencia::with('servico','unidade')
                            ->where('responsavel_id', Auth::id())
                            ->orWhereIn('pendencias.servico_id',$servicos)
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
                            // ->where('unidade.status','=','Ativa')
                            ->where('situacao','=','finalizado');  

         return $servicos;
    }

    public function servicosFinalizados()
    {
    	 $servicos = Servico::with('unidade','empresa','responsavel')
        						
        						// ->whereIn('unidade_id',$this->getUnidadesList())
                                ->orWhere('responsavel_id',Auth::id())
        						->get();


        $servicos = $servicos->where('situacao','=','finalizado')
                                // ->where('unidade.status','Ativa')
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
                                // ->where('unidade.status','Ativa')
                                ->where('situacao','<>','arquivado');

        return $servicos;
    }


}
