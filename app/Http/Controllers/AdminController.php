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
    		
        // dd($this->pendencias()->id);
		
		// return $this->pendencias();
        
        
        return view('admin.dashboard')
					->with([
						'vencer'=>$this->servicosVencer(),
						'finalizados'=>$this->servicosFinalizados(),
						'andamento'=>$this->servicosAndamento(),
                        'andamentoCoResponsavel'=>$this->servicosAndamentoCoResponsavel(),
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
                            ->whereDoesntHave('vinculos')
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

    public function servicosAndamentoCoResponsavel()
    {
        $servicos = Servico::with('unidade','empresa','responsavel')
                                
        // ->whereIn('unidade_id',$this->getUnidadesList())
        ->orWhere('coresponsavel_id',Auth::id())
        ->get();


        $servicos = $servicos->where('situacao','=','andamento')
        
        ->where('situacao','<>','arquivado');

        return $servicos;
    }


    public function relatorioCompleto()
    {
        
        $servicos = Servico::with('unidade','responsavel','financeiro','servicoFinalizado')
                            ->whereNotIn('responsavel_id',[1])
                            ->take(4000)
                            ->get();

        // dump($servicos);

        return view('admin.relatorios.completo')->with(['servicos'=>$servicos]);


    }


    public function completoCSV()
    { 
        $fileName = 'Celic_RelatorioCompleto_Servicos'.date('d-m-Y').'.csv';
        
        $servicos = Servico::with('unidade','responsavel','financeiro','servicoFinalizado')
        ->whereNotIn('responsavel_id',[1])
        // ->take(100)
        ->get();

        $headers = array(
            "Content-type"        => "text/csv",
            "Content-Disposition" => "attachment; filename=$fileName",
            "Pragma"              => "no-cache",
            "Cache-Control"       => "must-revalidate, post-check=0, pre-check=0",
            "Expires"             => "0"
        );

        $columns = array('Razão Social', 'Código', 'Nome', 'CNPJ', 'Status', 'Imóvel', 'Ins. Estadual', 'Ins.
        Municipal', 'Ins. Imob.', 'RIP', 'Matrícula RI', 'Área da Loja', 'Endereço', 'Número', 'Complemento',
        'Cidade/UF', 'CEP', 'Tipo', 'O.S.', 'Situação', 'Responsável', 'Co-Responsável', 'Nome', 'Solicitante',
        'Departamento', 'N° Protocolo', 'Emissão Protocolo', 'Tipo Licença', 'Proposta', 'Emissão Licença', 'Validade
        Licença', 'Valor Total', 'Valor em Aberto', 'Finalizado', 'Criação');

        $callback = function() use($servicos, $columns) {
            $file = fopen('php://output', 'w');
            fputcsv($file, $columns);

            foreach ($servicos as $s) {



                $cidadeUF = $s->unidade->cidade."/".$s->unidade->uf;

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

                fputcsv($file, array(
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
                    $cidadeUF,
                    $s->unidade->cep,
                    $s->tipo,
                    $s->os,
                    $s->situacao,
                    $s->responsavel->name,
                    $s->coresponsavel->name ?? '',
                    $s->nome,
                    $s->solicitante,
                    $s->departamento,
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

                ));
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    
    }


    public function taxasCSV()
    {   


             
        $fileName = 'Celic_RelatorioCompleto_Taxas'.date('d-m-Y').'.csv';
        
        $servicos = Servico::with('unidade','financeiro')->whereNotIn('responsavel_id',[1])->orderBy('id','DESC')->get();


        $headers = array(
            "Content-type"        => "text/csv",
            "Content-Disposition" => "attachment; filename=$fileName",
            "Pragma"              => "no-cache",
            "Cache-Control"       => "must-revalidate, post-check=0, pre-check=0",
            "Expires"             => "0"
        );

        $columns = array('Empresa','Serviço','OS','Código','Unidade','CNPJ','Cidade/UF','Proposta',
    'Valor Total','Taxa','Emissão','Vencimento','Pagamento','Reembolso','Status','Valor');

        $callback = function() use($servicos, $columns) {
            $file = fopen('php://output', 'w');
            fputcsv($file, $columns);

            foreach ($servicos as $s) {

                foreach($s->taxas as $t)
                {

                    $cidadeUF = $s->unidade->cidade."/".$s->unidade->uf;
       
                    if($s->proposta_id)
                    {
                        $proposta = $s->proposta_id;
                    }
                    else
                    {
                        $proposta = $s->proposta;
                    }
                

                    if($s->unidade->dataInauguraao)
                    {
                        $dataInauguracao =  \Carbon\Carbon::parse($s->unidade->dataInauguracao)->format('d/m/Y');
                    }
                    else
                    {
                        $dataInauguracao = null;
                    }
                    


            
                fputcsv($file, array(
                    $s->unidade->empresa->nomeFantasia,
                    $s->nome,
                    $s->os,
                    $s->unidade->codigo,
                    $s->unidade->nomeFantasia,
                    $s->unidade->cnpj,
                    $dataInauguracao,
                    $cidadeUF,
                    $proposta,
                    $s->financeiro->valorTotal ?? '',
                    $t->nome,
                    \Carbon\Carbon::parse($t->emissao)->translatedFormat('d-M-Y'),
                    \Carbon\Carbon::parse($t->vencimento)->translatedFormat('d-M-Y'),
                    \Carbon\Carbon::parse($t->pagamento)->translatedFormat('d-M-Y'),
                    $t->reembolso,
                    $t->situacao,
                    number_format($t->valor,2,",","."),

                    
                ));
                }

                
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    

    }

    public function pendenciasCSV()
    {
        
        $fileName = 'Celic_RelatorioCompleto_Pendencias'.date('d-m-Y').'.csv';
        
        $servicos = Servico::with('pendencias')
                            ->whereNotIn('responsavel_id',[1])
                            ->orderBy('id','DESC')
                            ->select('id','nome','os','unidade_id')
                            ->get();

        // $servicos = Pendencia::all();


        $headers = array(
            "Content-type"        => "text/csv",
            "Content-Disposition" => "attachment; filename=$fileName",
            "Pragma"              => "no-cache",
            "Cache-Control"       => "must-revalidate, post-check=0, pre-check=0",
            "Expires"             => "0"
        );

        $columns = array('Empresa','Serviço','OS','Código','Unidade','CNPJ','Data Inauguração','Cidade/UF','Pendência',
                        'Responsabilidade','Responsável','Status','Vencimento');

        $callback = function() use($servicos, $columns) {
            $file = fopen('php://output', 'w');
            fputcsv($file, $columns);

            foreach ($servicos as $s) {

                foreach($s->pendencias as $p)
                {

                    $cidadeUF = $s->unidade->cidade."/".$s->unidade->uf;

                
                                
                    if($s->proposta_id)
                    {
                        $proposta = $s->proposta_id;
                    }
                    else
                    {
                        $proposta = $s->proposta;
                    }


                    if($s->unidade->dataInauguraao)
                    {
                        $dataInauguracao =  \Carbon\Carbon::parse($s->unidade->dataInauguracao)->format('d/m/Y');
                    }
                    else
                    {
                        $dataInauguracao = null;
                    }
                
                    

                fputcsv($file, array(
                    $s->unidade->empresa->nomeFantasia,
                    $s->nome,
                    $s->os,
                    $s->unidade->codigo,
                    $s->unidade->nomeFantasia,
                    $s->unidade->cnpj,
                    $dataInauguracao,
                    $cidadeUF,
                    $p->pendencia,
                    $p->responsavel_tipo,
                    $p->responsavel->name ?? '',
                    $p->status,
                    \Carbon\Carbon::parse($p->vencimento)->format('d/m/Y'),

                    
                ));
                }              
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);

        // return $servicos;
    

    }


    


}