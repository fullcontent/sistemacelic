<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Auth;
use App\User;
use App\Models\Servico;
use App\Models\Pendencia;

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
    	$user = Auth::id();

    	

    		
		$vencer = Servico::where('licenca_validade','<',\Carbon\Carbon::today()->addDays(60))->where('situacao','finalizado')->where('responsavel_id',$user)->get();
		$vencer = $vencer->where('unidade.status','Ativa');


		$finalizados = Servico::where('situacao','finalizado')->where('responsavel_id',$user)->get();
		$finalizados = $finalizados->where('unidade.status','Ativa');


		$pendencias = Pendencia::with('servico','unidade')->where('responsavel_id',$user)->get();


		
		
		return view('admin.dashboard')
					->with([
						'vencer'=>$vencer,
						'finalizados'=>$finalizados,
						'pendencias'=>$pendencias,
						
					]);
    }


}
