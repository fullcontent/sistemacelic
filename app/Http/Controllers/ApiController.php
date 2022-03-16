<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\Empresa;
use App\Models\Unidade;
use App\Models\ServicoLpu;
use App\User;

class ApiController extends Controller
{
   public function getUnidades(Request $request){

        $search = $request->search;
  
        if($search == ''){
           $unidades = Unidade::orderby('nomeFantasia','asc')->select('id','nomeFantasia')->get();
        }else{
           $unidades = Unidade::orderby('nomeFantasia','asc')->select('id','nomeFantasia')->where('nomeFantasia', 'like', '%' .$search . '%')->get();
        }
  
        $response = array();
        foreach($unidades as $u){
           $response[] = array(
                "id"=>$u->id,
                "text"=>$u->nomeFantasia,
            
           );
        }
  
        return response()->json($response);
     }


     public function getResponsaveis(Request $request){

        $search = $request->search;
  
        if($search == ''){
           $responsaveis = User::orderby('name','asc')->select('id','name')->get();
        }else{
           $responsaveis = User::orderby('name','asc')->select('id','name')->where('name', 'like', '%' .$search . '%')->get();
        }
  
        $response = array();
        foreach($responsaveis as $u){
           $response[] = array(
                "id"=>$u->id,
                "text"=>$u->name,
                
           );
        }
  
        return response()->json($response);
     }

     public function getServicosLpu(Request $request){

      $search = $request->search;

      if($search == ''){
         $servicos = ServicoLpu::orderby('nomeCelic','asc')->select('id','nomeCelic','processo')->get();
      }else{
         $servicos = ServicoLpu::orderby('nomeCelic','asc')->select('id','nomeCelic','processo')->where('nomeCelic', 'like', '%' .$search . '%')->get();
      }

      $response = array();
      foreach($servicos as $u){
         $response[] = array(
              "id"=>$u->id,
              "text"=>$u->nomeCelic." - ".$u->processo,
              
         );
      }

      return response()->json($response);
   }

   public function getServicoLpuById(Request $request){


      
      
      $servico = ServicoLpu::orderby('nome','asc')->select('id','nome','escopo','valor')->find($request->id);


      $response = array();
      $response[] = array(
              "id"=>$servico->id,
              "nome"=>$servico->nome,
              "escopo"=>$servico->escopo,
              "valor"=>$servico->valor,
              
      );
      
      

      return response()->json($response);
   }
}
