<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\Empresa;
use App\Models\Unidade;
use App\Models\ServicoLpu;
use App\Models\Servico;
use App\Models\Reembolso;
use App\Models\Faturamento;
use App\Models\Proposta;

use App\User;
use App\Models\DadosCastro;
use Auth;

class ApiController extends Controller
{
   // Function to get all unidades from the database based on a user search input 
   public function getUnidades(Request $request){
       
       // Get search input
       $search = $request->search;
     
       // Create empty array for response
       $response = array();
   
       // If there is no search input 
       if($search == ''){
          // Get all unidades in the database, order by nameFantasia and select only their id and nameFantasia 
          $unidades = Unidade::orderby('nomeFantasia','asc')->select('id','nomeFantasia')->get();
       }else{
          // Get only unidade where nomeFantasia matches search input
          $unidades = Unidade::orderby('nomeFantasia','asc')->select('id','nomeFantasia')->where('nomeFantasia', 'like', '%' .$search . '%')->get();
       }
     
       // Loop through each unidade
       foreach($unidades as $u){
           // Add corresponding id and nameFantasia to the response array 
           $response[] = array(
               "id"=>$u->id,
               "text"=>$u->nomeFantasia,
           
           );
       }
     
       // Return response array as JSON
       return response()->json($response);
   }
   

     // function getEmpresas() : retrieves a list of companies from the database 
     // based on the search parameter passed from the request
     public function getEmpresas(Request $request){
     
           // accept a search parameter from the Request object
           $search = $request->search;
     
           // if no search parameter is included, return all values in the table
           if($search == ''){
              $empresas = Empresa::orderby('nomeFantasia','asc')->select('id','nomeFantasia')->get();
           }else{
              // otherwise retrieve results based on the search string
              $empresas = Empresa::orderby('nomeFantasia','asc')->select('id','nomeFantasia')->where('nomeFantasia', 'like', '%' .$search . '%')->get();
           }
     
           // Initialize an empty response array
           $response = array();
     
           // loop through each result and create and add a subarray of id/text to response array
           foreach($empresas as $u){
              $response[] = array(
                   "id"=>$u->id,
                   "text"=>$u->nomeFantasia,
               
              );
           }
     
           // return the response array in json format
           return response()->json($response);
        }
     


     // Function to get all the responsaveis 
     public function getResponsaveis(Request $request) {
         // getting input search value into a variable
         $search = $request->search;
     
         // if the search query is empty, return all the responsaveis
         if($search == ''){
            $responsaveis = User::orderby('name','asc')->select('id','name')->get();
         }else{
            $responsaveis = User::orderby('name','asc')->select('id','name')->where('name', 'like', '%' .$search . '%')->get();
         }
     
         // response array will store the responsaveis information
         $response = array();
     
         // loop through all the responsaveis
         foreach($responsaveis as $u){
            $response[] = array(
                 "id"=>$u->id,
                 "text"=>$u->name,
                 
            );
         }
     
         // return the response as json
         return response()->json($response);
      }
     

     public function getServicosLpu(Request $request){

      $search = $request->search;

      if($search == ''){
         $servicos = ServicoLpu::orderby('nome','asc')->select('id','nome','processo')->get();
      }else{
         $servicos = ServicoLpu::orderby('nome','asc')->select('id','nome','processo')->where('nome', 'like', '%' .$search . '%')->get();
      }

      $response = array();
      foreach($servicos as $u){
         $response[] = array(
              "id"=>$u->id,
              "text"=>$u->nome." - ".$u->processo,
              
         );
      }

      return response()->json($response);
   }

   public function getServicoLpuById(Request $request){


      
      
      $servico = ServicoLpu::orderby('nome','asc')->select('id','nomeCelic','escopo','valor')->find($request->id);


      $response = array();
      $response[] = array(
              "id"=>$servico->id,
              "nome"=>$servico->nomeCelic,
              "escopo"=>$servico->escopo,
              "valor"=>$servico->valor,
              
      );
      
      

      return response()->json($response);
   }

   public function getAllServicesJSON()
    { 
               
        $servicos = Servico::with('unidade','responsavel','financeiro','servicoFinalizado','vinculos')
        ->whereNotIn('responsavel_id',[1])
        // ->take(100)
        ->get();
        

        $data = [];

        $columns = array('ServicoID','Razão Social', 'Código', 'Nome', 'CNPJ', 'Status', 'Imóvel', 'Ins. Estadual', 'Ins. Municipal', 'Ins. Imob.', 'RIP', 'Matrícula RI', 'Área da Loja', 'Endereço', 'Número', 'Complemento','Data Inauguração',
        'Cidade','UF', 'CEP', 'Tipo', 'O.S.', 'Situação', 'Responsável', 'Co-Responsável', 'Nome', 'Solicitante','Departamento','Licenciamento', 'N° Protocolo', 'Emissão Protocolo', 'Tipo Licença', 'Proposta', 'Emissão Licença', 'Validade Licença', 'Valor Total', 'Valor em Aberto', 'Finalizado', 'Criação');


        
       
        foreach ($servicos as $s) {

         if(is_numeric($s->solicitante))
         {
           $s->solicitante =  \App\Models\Solicitante::where('id',$s->solicitante)->value('nome');
         }
                         
             if($s->proposta_id)
             {
                 $proposta = $s->proposta_id;
             }
             else
             {
                 $proposta = $s->proposta;
             }
         
             if(isset($s->servicoFinalizado)){
                 $finalizado = \Carbon\Carbon::parse($s->servicoFinalizado->finalizado)->format('d/m/Y');
             }
             else{
                 $finalizado = '';
             }

             if(isset($s->protocolo_emissao))
             {
                 $protocolo_emissao = \Carbon\Carbon::parse($s->protocolo_emissao)->format('d/m/Y');
             }
             else{
                 $protocolo_emissao = null;
             }

             if(isset($s->licenca_emissao))
             {
                 $licenca_emissao = \Carbon\Carbon::parse($s->licenca_emissao)->format('d/m/Y');
             }
             else{
                 $licenca_emissao = null;
             }
             if(isset($s->licenca_validade))
             {
                 $licenca_validade = \Carbon\Carbon::parse($s->licenca_validade)->format('d/m/Y');
             }
             else{
                 $licenca_validade = null;
             }


             if($s->unidade->dataInauguraao)
             {
                 $dataInauguracao =  \Carbon\Carbon::parse($s->unidade->dataInauguracao)->format('d/m/Y');
             }
             else
             {
                 $dataInauguracao = null;
             }
             
            
             array_push($data, [
                $s->id,
               $s->unidade->razaoSocial,
               $s->unidade->codigo,
               $s->unidade->nomeFantasia,
               $s->unidade->cnpj,
               $s->unidade->status,
               $s->unidade->tipoImovel,
               $s->unidade->inscricaoEst,
               $s->unidade->inscricaoMun,
               $s->unidade->inscricaoImo,
               $s->unidade->rip,
               $s->unidade->matriculaRI,
               $s->unidade->area,
               $s->unidade->endereco,
               $s->unidade->numero,
               $s->unidade->complemento,
               $dataInauguracao,
               $s->unidade->cidade,
               $s->unidade->uf,
               $s->unidade->cep,
               $s->tipo,
               $s->os,
               $s->situacao,
               $s->responsavel->name,
               $s->coresponsavel->name ?? '',
               $s->nome,
               $s->solicitante,
               $s->departamento,
               $s->licenciamento ?? '',
               $s->protocolo_numero,
               $protocolo_emissao,
               $s->tipoLicenca,
               $proposta,
               $licenca_emissao,
               $licenca_validade,
               $s->financeiro->valorTotal ?? '0',
               $s->financeiro->valorAberto ?? '0',
               $finalizado,
               \Carbon\Carbon::parse($s->created_at)->format('d/m/Y') ?? '',
               
             
            
             ]);
            
     }
      return response()->json($data);

    
    }

    public function getClienteServicesJSON()
    { 
               
        $user = User::find(Auth::id());


        if(count($user->empresas))
        {

            $unidades = Unidade::where('empresa_id', $user->empresas->pluck('id'))->pluck('id');

            $servicos = Servico::orWhereIn('empresa_id',$user->empresas->pluck('id'))
                                ->orWhereIn('unidade_id', $unidades)
                                ->with('unidade','responsavel','financeiro','servicoFinalizado','vinculos')
                                ->get();
    
           
        }

        
       
       
       
       
       
        // $servicos = Servico::with('unidade','responsavel','financeiro','servicoFinalizado','vinculos')
        // ->whereNotIn('responsavel_id',[1])
        // ->where('responsavel_id')
        // // ->take(100)
        // ->get();
        

        $data = [];

        $columns = array('ServicoID','Razão Social', 'Código', 'Nome', 'CNPJ', 'Status', 'Imóvel', 'Ins. Estadual', 'Ins. Municipal', 'Ins. Imob.', 'RIP', 'Matrícula RI', 'Área da Loja', 'Endereço', 'Número', 'Complemento','Data Inauguração',
        'Cidade','UF', 'CEP', 'Tipo', 'O.S.', 'Situação', 'Responsável', 'Co-Responsável', 'Nome', 'Solicitante','Departamento','Licenciamento', 'N° Protocolo', 'Emissão Protocolo', 'Tipo Licença', 'Proposta', 'Emissão Licença', 'Validade Licença', 'Valor Total', 'Valor em Aberto', 'Finalizado', 'Criação');


        
       
        foreach ($servicos as $s) {

         if(is_numeric($s->solicitante))
         {
           $s->solicitante =  \App\Models\Solicitante::where('id',$s->solicitante)->value('nome');
         }
                         
             if($s->proposta_id)
             {
                 $proposta = $s->proposta_id;
             }
             else
             {
                 $proposta = $s->proposta;
             }
         
             if(isset($s->servicoFinalizado)){
                 $finalizado = \Carbon\Carbon::parse($s->servicoFinalizado->finalizado)->format('d/m/Y');
             }
             else{
                 $finalizado = '';
             }

             if(isset($s->protocolo_emissao))
             {
                 $protocolo_emissao = \Carbon\Carbon::parse($s->protocolo_emissao)->format('d/m/Y');
             }
             else{
                 $protocolo_emissao = null;
             }

             if(isset($s->licenca_emissao))
             {
                 $licenca_emissao = \Carbon\Carbon::parse($s->licenca_emissao)->format('d/m/Y');
             }
             else{
                 $licenca_emissao = null;
             }
             if(isset($s->licenca_validade))
             {
                 $licenca_validade = \Carbon\Carbon::parse($s->licenca_validade)->format('d/m/Y');
             }
             else{
                 $licenca_validade = null;
             }


             if($s->unidade->dataInauguraao)
             {
                 $dataInauguracao =  \Carbon\Carbon::parse($s->unidade->dataInauguracao)->format('d/m/Y');
             }
             else
             {
                 $dataInauguracao = null;
             }
             
            
             array_push($data, [
                $s->id,
               $s->unidade->razaoSocial,
               $s->unidade->codigo,
               $s->unidade->nomeFantasia,
               $s->unidade->cnpj,
               $s->unidade->status,
               $s->unidade->tipoImovel,
               $s->unidade->inscricaoEst,
               $s->unidade->inscricaoMun,
               $s->unidade->inscricaoImo,
               $s->unidade->rip,
               $s->unidade->matriculaRI,
               $s->unidade->area,
               $s->unidade->endereco,
               $s->unidade->numero,
               $s->unidade->complemento,
               $dataInauguracao,
               $s->unidade->cidade,
               $s->unidade->uf,
               $s->unidade->cep,
               $s->tipo,
               $s->os,
               $s->situacao,
               $s->responsavel->name ?? '',
               $s->coresponsavel->name ?? '',
               $s->nome,
               $s->solicitante,
               $s->departamento,
               $s->licenciamento ?? '',
               $s->protocolo_numero,
               $protocolo_emissao,
               $s->tipoLicenca,
               $proposta,
               $licenca_emissao,
               $licenca_validade,
               $s->financeiro->valorTotal ?? '0',
               $s->financeiro->valorAberto ?? '0',
               $finalizado,
               \Carbon\Carbon::parse($s->created_at)->format('d/m/Y') ?? '',
               
             
            
             ]);
            
     }
      return response()->json($data);

    
    }



    public function getRazaoSocial(Request $request)
    {
              
        $data = Empresa::find($request->empresa_id);

       return response()->json($data);
    }

    public function getPendenciasFromUnidade(Request $request)
    {
        
        $unidade = Unidade::find($request->unidade_id);

        $data = $unidade->pendencias->pluck('id','pendencia');

        return response()->json($data);    
    }

    public function getDadosCastro()
    {
        $data = DadosCastro::all();

        return response()->json($data);
    }

    public function saveDadosCastro(Request $request)
    {
        


        if($request->faturamento_id)
        {
            $faturamento = Faturamento::find($request->faturamento_id);
            $faturamento->dadosCastro_id = $request->dadosCastro_id;
            $faturamento->save();
        }
        
        if($request->reembolso_id)
        {
            $reembolso = Reembolso::find($request->reembolso_id);
            $reembolso->dadosCastro_id = $request->dadosCastro_id;
            $reembolso->save();
        }

        if($request->proposta_id)
        {
            $proposta = Proposta::find($request->proposta_id);
            $proposta->dadosCastro_id = $request->dadosCastro_id;
            $proposta->save();
        }


       

    }
}
