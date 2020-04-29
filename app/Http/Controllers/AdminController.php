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

    public function getUnidadesList()
    {
        $unidadesList = Unidade::where('empresa_id', UserAccess::where('user_id',Auth::id())->pluck('empresa_id'))->pluck('id');

        return $unidadesList;
    }

    public function pendencias()
    {	

    	
    		$servicos = Servico::whereIn('unidade_id',$this->getUnidadesList())->pluck('id');
    		$pendencias = Pendencia::with('servico','unidade')
    						// ->where('responsavel_id', Auth::id())
    						->whereIn('servico_id', $servicos)
    						->orWhere('responsavel_id',Auth::id())
    						->get();

        	
        	return $pendencias;
    }

    public function servicosVencer()
    {
    	$servicos = Servico::with('unidade','empresa','responsavel')
                                ->whereIn('unidade_id',$this->getUnidadesList())
                                ->orWhere('responsavel_id',Auth::id())
                                ->get();

        $servicos = $servicos->where('licenca_validade','<',\Carbon\Carbon::today()->addDays(60))
                            ->where('unidade.status','=','ativa')
                            ->where('situacao','=','finalizado');  

         return $servicos;
    }

    public function servicosFinalizados()
    {
    	 $servicos = Servico::with('unidade','empresa','responsavel')
        						
        						->whereIn('unidade_id',$this->getUnidadesList())
                                ->orWhere('responsavel_id',Auth::id())
        						->get();


        $servicos = $servicos->where('situacao','=','finalizado')
                                ->where('unidade.status','ativa')
                                ->where('situacao','<>','arquivado');

         return $servicos;
    }

    public function servicosAndamento()
    {
    	 $servicos = Servico::with('unidade','empresa','responsavel')
                                
                                ->whereIn('unidade_id',$this->getUnidadesList())
                                ->orWhere('responsavel_id',Auth::id())
                                ->get();


        $servicos = $servicos->where('situacao','=','andamento')
                                ->where('unidade.status','ativa')
                                ->where('situacao','<>','arquivado');

        return $servicos;
    }


}
