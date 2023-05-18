<?php

namespace App\Http\Controllers;

use Auth;
use Mail;
use App\User;
use Carbon\Carbon;
use App\UserAccess;
use App\Models\Arquivo;
use App\Models\Empresa;
use App\Models\Servico;
use App\Models\Unidade;
use App\Models\Historico;
use App\Models\ServicoLpu;
use App\Models\Pendencia;
use App\Models\Solicitante;
use App\Models\PendenciasVinculos;




use App\Models\ServicoFinanceiro;
use App\Models\ServicoFinalizado;
use App\Models\Faturamento;


use App\Notifications\UserMentioned;
use App\Mail\UsuarioMencionado;
use Illuminate\Support\Facades\Notification;


use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;


class ServicosController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */

    public function __construct()
    {
        
        $this->middleware('admin');

    }

    
    public function index()
    {
        
        return view('admin.lista-servicos-geral');
    }

    public function lista()
    {
       

       $servicos = Servico::with('unidade','empresa','responsavel')
                                
                                // ->whereIn('unidade_id',$this->getUnidadesList())

                                ->where('responsavel_id',Auth::id())
                                ->orWhere('coresponsavel_id', Auth::id())
                                ->get();


        $servicos = $servicos->where('situacao','<>','arquivado');
                               
      



        return view('admin.lista-servicos')
                    ->with('servicos',$servicos);
    }

    public function listaAndamento()
    {
        
        $servicos = Servico::with('unidade','empresa','responsavel')
                                
                                // ->whereIn('unidade_id',$this->getUnidadesList())
                                ->orWhere('responsavel_id',Auth::id())
                                ->orWhere('coresponsavel_id', Auth::id())
                                ->get();


        $servicos = $servicos->where('situacao','=','andamento')
                                
                                ->where('situacao','<>','arquivado');



        return view('admin.lista-servicos')
                    ->with('servicos',$servicos);
    }

    public function listaAndamentoCo()
    {
        
        $servicos = Servico::with('unidade','empresa','responsavel')
                                
                                // ->whereIn('unidade_id',$this->getUnidadesList())
                                ->orWhere('coresponsavel_id',Auth::id())
                                ->orWhere('coresponsavel_id', Auth::id())
                                ->get();


        $servicos = $servicos->where('situacao','=','andamento')
                                
                                ->where('situacao','<>','arquivado');



        return view('admin.lista-servicos')
                    ->with('servicos',$servicos);
    }

    public function listaFinalizados()
    {
        
                
        $servicos = Servico::with('unidade','empresa','responsavel')
        						
        						// ->whereIn('unidade_id',$this->getUnidadesList())
                                ->orWhere('responsavel_id',Auth::id())
                                ->orWhere('coresponsavel_id', Auth::id())
        						->get();


        $servicos = $servicos->where('situacao','=','finalizado')
                                
                                ->where('situacao','<>','arquivado');
        

        				      

        return view('admin.lista-servicos')
                    ->with('servicos',$servicos);
    }

    public function listaVigentes()
    {
        $servicos = Servico::with('unidade','empresa','responsavel')
                                // ->whereIn('unidade_id',$this->getUnidadesList())
                                ->orWhere('responsavel_id',Auth::id())
                                ->orWhere('coresponsavel_id', Auth::id())
                                ->get();


        $servicos = $servicos->where('unidade.status','=','Ativa')
                                ->where('licenca_validade','>',date('Y-m-d'))
                                ->where('tipo','licencaOperacao')
                                ->where('situacao','<>','arquivado');

        return view('admin.lista-servicos')
                    ->with('servicos',$servicos);
    }

    public function listaArquivados()
    {   

        
        
        $servicos = Servico::with('unidade','empresa','responsavel')
                                // ->whereIn('unidade_id',$this->getUnidadesList())
                                ->orWhere('responsavel_id',Auth::id())
                                ->orWhere('coresponsavel_id', Auth::id())
                                ->get();
       
        $servicos = $servicos->where('situacao','=','arquivado');
        

        return view('admin.lista-servicos')
                    ->with('servicos',$servicos);
    }

    public function listaNRenovados()
    {   

        
        
        $servicos = Servico::with('unidade','empresa','responsavel')
                                // ->whereIn('unidade_id',$this->getUnidadesList())
                                ->orWhere('responsavel_id',Auth::id())
                                ->orWhere('coresponsavel_id', Auth::id())
                                ->get();
       
        $servicos = $servicos->where('situacao','=','nRenovado');
        

        return view('admin.lista-servicos')
                    ->with('servicos',$servicos);
    }

    public function listaVencidos()
    {   

               
        $servicos = Servico::with('unidade','empresa','responsavel')
                                // ->whereIn('unidade_id',$this->getUnidadesList())
                                ->orWhere('responsavel_id',Auth::id())
                                ->orWhere('coresponsavel_id', Auth::id())
                                ->get();
        
       $servicos = $servicos->where('unidade.status','=','Ativa')
                            ->where('licenca_validade','<',date('Y-m-d'))
                            ->where('tipo','=','licencaOperacao')
                            ->where('situacao','<>','arquivado');

       


        return view('admin.lista-servicos')
                    ->with('servicos',$servicos);
    }

    public function listaVencer()
    {   

        $servicos = Servico::with('unidade','empresa','responsavel')
                                // ->whereIn('unidade_id',$this->getUnidadesList())
                                ->orWhere('responsavel_id',Auth::id())
                                ->orWhere('coresponsavel_id', Auth::id())
                                ->get();

        $servicos = $servicos->where('licenca_validade','<',\Carbon\Carbon::today()->addDays(60))
                            
                            ->where('situacao','=','finalizado') 
                            ->where('tipo','=','licencaOperacao');       
        
       

       // $servicos = $servicos->where('unidade.status','Ativa')->where('situacao','Finalizado');

        return view('admin.lista-servicos')
                    ->with('servicos',$servicos);
    }

    public function listaInativo()
    {
        
               
        $servicos = Servico::with('unidade','empresa','responsavel')
                                // ->whereIn('unidade_id',$this->getUnidadesList())
                                ->orWhere('responsavel_id',Auth::id())
                                ->orWhere('coresponsavel_id', Auth::id())
                                ->get();

       $servicos = $servicos->where('unidade.status','=','Inativa');

        return view('admin.lista-servicos')
                    ->with('servicos',$servicos);
    }


    public function renovar($id)
    {
        $servico = Servico::find($id);
        $servico->situacao = 'arquivado';
        $servico->save();

        
        if($servico->unidade_id)
        {

           $string = $servico->os;

            $lastOS = Servico::where('os','like', '%'.$string.'%')->orderBy('os','DESC')->pluck('os')->first();
            $count = strlen($string);
            $i = 0;
            while( $i < $count ) {
                if( ctype_digit($string[$i]) ) {
                    // echo "First digit found at position $i.";
                    $os = substr($lastOS, 0, $i);
                    $number = substr($lastOS, $i, 4);
                    $number = $number + 1;
                    $os .= $number;
                }
                $i++;
                }
                


        }

        
              


        $newService = new Servico;

        $newService->nome = $servico->nome;
        $newService->tipo = $servico->tipo;        
        $newService->responsavel_id = $servico->responsavel_id;
        $newService->observacoes = $servico->observacoes;

        $newService->unidade_id = $servico->unidade_id;
        $newService->licenca_emissao = $servico->licenca_emissao;
        $newService->licenca_validade = $servico->licenca_validade;

        $newService->situacao = 'andamento';
        $newService->os = $os;


        $newService->save();


        if($servico->taxas)
        {

            foreach($servico->taxas as $t)
            {
                
                if($t->boleto)
                {
                $arquivoTaxa = new Arquivo;
                $arquivoTaxa->servico_id = $servico->id;
                $arquivoTaxa->unidade_id = $servico->unidade_id;
                $arquivoTaxa->nome = $t->nome;
                $arquivoTaxa->arquivo = $t->boleto;
                $arquivoTaxa->save(); 
                }

                

                if($t->comprovante)
                {
                    $arquivoTaxa = new Arquivo;
                    $arquivoTaxa->servico_id = $servico->id;
                    $arquivoTaxa->unidade_id = $servico->unidade_id;
                    $arquivoTaxa->nome = 'Comprovante '.$t->nome;
                    $arquivoTaxa->arquivo = $t->comprovante;
                    $arquivoTaxa->save();
                }
            }


        }


        $arquivoDigital = new Arquivo;
        $arquivoDigital->servico_id = $servico->id;
        $arquivoDigital->unidade_id = $servico->unidade_id;
        $arquivoDigital->nome = $servico->nome.' - '.Carbon::create($servico->licenca_validade)->format('d-m-Y');
        $arquivoDigital->arquivo = $servico->licenca_anexo;
        $arquivoDigital->save();




        return redirect()->route('servicos.edit',$newService->id);
        

    }


    public function desconsiderar($id)
    {
        $servico = Servico::find($id);

        $servico->situacao = 'nRenovado';
        $servico->save();


        //Insert history

        $history = new Historico();
        $history->servico_id = $servico->id;
        $history->user_id = Auth::id();
        $history->observacoes = "Serviço ".$servico->id." não renovado.";
        $history->created_at = Carbon::now('america/sao_paulo');
        $history->save();


        return redirect()->route('servico.vencer');
    }



    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {
        //

        
        $tipo = $request->t;
        $tipoServico = $request->tipoServico;
    
        $id = $request->id;
        $users = User::where('privileges','=','admin')->where('active',1)->orderBy('name')->pluck('name','id')->toArray();

        $servico = null;
        $servicoPrincipal=null;
        
        
        if(isset($request->servicoPrincipal))
        {
            $servicoPrincipal = $request->servicoPrincipal;
        }

        switch ($tipo) {
            case 'unidade':
                // code...
                    $u = Unidade::find($id);
                    $a = $u->empresa->razaoSocial;
                    $a = explode(' ',$a);
                    $os = substr($a[0], 0, 1);
                    $os .= substr($a[1], 0, 1); 

                    $lastOS = Servico::where('os','like','%'.$os.'%')->orderBy('os','DESC')->pluck('os')->first();

                    if(!$lastOS)
                    {
                        $number = "0001";
                        

                    }
                    else {

                        $number = substr($lastOS, 2,4);
                        $number = str_pad($number+1, 4, "000", STR_PAD_LEFT);    
                                                      
                        
                    }

                    

                    $os .= $number;

                    // $servico_lpu = ServicoLpu::where('empresa_id',$u->empresa->id)->pluck('documento','id')->toArray();


                break;
            case 'empresa':

                    $u = Empresa::find($id);
                    $a = $u->razaoSocial;
                    $a = explode(' ',$a);
                    $os = substr($a[0], 0, 1);
                    $os .= substr($a[1], 0, 1); 

                    $lastOS = Servico::where('os','like','%'.$os.'0%')->orderBy('os','DESC')->pluck('os')->first();

                     if(!$lastOS)
                    {
                        $number = "0001";

                    }
                    else {
                        $number = substr($lastOS, 2,4);
                        $number = str_pad($number+1, 4, "000", STR_PAD_LEFT);
                        
                    }

                    $os .= $number;

                    // $servico_lpu = ServicoLpu::where('empresa_id',$u->id)->pluck('documento','id')->toArray();

                break;
            
            
        }


        $solicitantes = Solicitante::whereHas('empresas',function($q) use($u){
            $q->where('empresa_id', '=', $u->empresa_id);
        })->orderBy('nome')->get()->pluck('nome','id');   


        return view('admin.cadastro-servico')
                ->with([
                    't'=>$tipo,
                    'id'=>$id,
                    'users'=>$users,
                    'os' => $os,
                    'servico'=>$servico,
                    // 'servico_lpu'=>$servico_lpu,
                    'tipoServico'=>$tipoServico,
                    'servicoPrincipal'=>$servicoPrincipal,
                    'solicitantes'=>$solicitantes,
                    
                    
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
        

        $request->validate([
            'valorTotal' => 'required',
            'os'=>'required',
            'nome'=>'required',
            'solicitante'=>'required',
            
        ]);
        

       
        
        
        $servico = new Servico;
        $servico->tipo  =   $request->tipo;
        $servico->os    =   $request->os;
        $servico->nome  =   $request->nome;
        $servico->situacao  =  $request->situacao;
        $servico->responsavel_id = $request->responsavel_id;
        $servico->coresponsavel_id = $request->coresponsavel_id;
               
        $servico->protocolo_numero  =   $request->protocolo_numero;

        if($request->licenciamento)
        {
            $servico->licenciamento  =   $request->licenciamento;
        }
        
       
        
        $servico->observacoes   = $request->observacoes;
        $servico->escopo   = $request->escopo;
        $servico->proposta   = $request->proposta;
        $servico->solicitante = $request->solicitante;
        $servico->departamento = $request->departamento;
        $servico->servico_lpu = $request->servico_lpu;
        $servico->tipoLicenca = $request->tipoLicenca;
        

        $servico->laudo_numero = $request->laudo_numero;

        

        
        if($request->protocolo_emissao)
        {
            $servico->protocolo_emissao = Carbon::createFromFormat('d/m/Y', $request->protocolo_emissao)->toDateString();
        }

        if($request->licenca_emissao)
        {
            $servico->licenca_emissao = Carbon::createFromFormat('d/m/Y', $request->licenca_emissao)->toDateString();
        }
        if($request->licenca_validade)
        {
            $servico->licenca_validade = Carbon::createFromFormat('d/m/Y', $request->licenca_validade)->toDateString();
        }

        if($request->laudo_emissao)
        {
            $servico->laudo_emissao = Carbon::createFromFormat('d/m/Y', $request->laudo_emissao)->toDateString();
        }

        

        //Upload de anexos com md5

         // Se informou o arquivo, retorna um boolean
        
        if ($request->hasFile('licenca_anexo') && $request->file('licenca_anexo')->isValid()) {
                $nameFile = null;
                $name = uniqid(date('HisYmd'));
                $extension = $request->licenca_anexo->extension();
                $nameFile = "{$name}.{$extension}";
                // Faz o upload:
                $upload = $request->licenca_anexo->storeAs('licencas', $nameFile);
                // Se tiver funcionado o arquivo foi armazenado em storage/app/public/categories/nomedinamicoarquivo.extensao

                $servico->licenca_anexo = $upload;

            }

         // Se informou o arquivo, retorna um boolean
        if ($request->hasFile('protocolo_anexo') && $request->file('protocolo_anexo')->isValid()) {
                $nameFile = null;
                $name = uniqid(date('HisYmd'));
                $extension = $request->protocolo_anexo->extension();

                $nameFile = "{$name}.{$extension}";
                // Faz o upload:
                $upload = $request->protocolo_anexo->storeAs('protocolos', $nameFile);
                // Se tiver funcionado o arquivo foi armazenado em storage/app/public/categories/nomedinamicoarquivo.extensao

                $servico->protocolo_anexo = $upload;


            }

        if ($request->hasFile('laudo_anexo') && $request->file('laudo_anexo')->isValid()) {
                $nameFile = null;
                $name = uniqid(date('HisYmd'));
                $extension = $request->laudo_anexo->extension();

                $nameFile = "{$name}.{$extension}";
                // Faz o upload:
                $upload = $request->laudo_anexo->storeAs('laudos', $nameFile);
                // Se tiver funcionado o arquivo foi armazenado em storage/app/public/categories/nomedinamicoarquivo.extensao

                $servico->laudo_anexo = $upload;


            }


         

        if($servico->tipoLicenca == 'n/a' || $servico->tipoLicenca == 'definitiva')
        {
            
            $servico->licenca_validade = '2059-12-31';
        }


        


        if($request->t == 'unidade')
        {
            $servico->unidade_id = $request->unidade_id;
        }

        if($request->t == 'empresa')
        {
            $servico->unidade_id = $request->empresa_id;
        }
        

        $servico->servicoPrincipal = $request->servicoPrincipal;


              
        $servico->save();
        

       


        //Insert Financeiro

        
        $faturamento = new ServicoFinanceiro();
        $faturamento->servico_id = $servico->id;
        
        $faturamento->valorTotal = $request->valorTotal;

        //Inserir valor em aberto igual o valor total na criação.

        $faturamento->valorAberto = $request->valorTotal;


        

        $faturamento->save();      


        


        //Insert history

        $history = new Historico();
        $history->servico_id = $servico->id;
        $history->user_id = Auth::id();
        $history->observacoes = "Serviço ".$servico->id." cadastrado.";
        $history->created_at = Carbon::now('america/sao_paulo');
        $history->save();

        




        return redirect()->route('pendencia.create',$servico->id);



        
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
        $servico = Servico::with('servicoPrincipal')->find($id);


                   
        //Check if is empresa or unidade

        if($servico->unidade_id){

            $dados = $servico->unidade;
            $route = 'unidades.edit';
        }
        else{
            $dados = $servico->empresa;
            $route = 'empresas.edit';
        }

        $access = UserAccess::where('user_id',Auth::id())->whereNull('unidade_id')->pluck('empresa_id');
        $empresa = Empresa::where('id',$servico->unidade->empresa_id)->pluck('id');
        

        

        if($access->contains($empresa[0]))
        {
            return view('admin.detalhe-servico')
            ->with([
                'servico'=>$servico,
                'dados'=>$dados,
                'route'=>$route,
                'taxas'=>$servico->taxas,
                'pendencias'=>$servico->pendencias,
                'arquivo'=>'servico',
                'usuarios'=>User::pluck('name','id')->toArray(),
                'ordensCompra'=>$servico->ordensCompra,
                
            ]);

        }
        else
        {
            return view('errors.403');
        }
            
       

  



       
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        

        $servico = Servico::find($id);
                
       $users = User::where('privileges','=','admin')->where('active',1)->orderBy('name')->pluck('name','id')->toArray();

        // $servico_lpu = ServicoLpu::where('empresa_id',$servico->unidade->empresa->id)->pluck('documento','id')->toArray();

        $servico_lpu = null;

        //Check if is empresa or unidade

        if($servico->unidade_id){

            $dados = $servico->unidade;
            $route = 'unidades.edit';
        }
        else{
            $dados = $servico->empresa;
            $route = 'empresas.edit';
        }


        //Change date format to dd/mm/YYYY

        if($servico->protocolo_emissao)
        {
            $servico->protocolo_emissao = date('d/m/Y',strtotime($servico->protocolo_emissao));
        }

        

        if($servico->licenca_emissao)
        {
             $servico->licenca_emissao = date('d/m/Y',strtotime($servico->licenca_emissao));
        }

        if($servico->licenca_validade)
       {
        $servico->licenca_validade = date('d/m/Y',strtotime($servico->licenca_validade));
       }

       if($servico->laudo_emissao)
       {
        $servico->laudo_emissao = date('d/m/Y',strtotime($servico->laudo_emissao));
       }
        

       if(!$servico->financeiro)
       {
           
            $financeiro = new ServicoFinanceiro();
            $financeiro->servico_id = $servico->id;
            $financeiro->valorTotal = 0;
            $financeiro->valorFaturado = 0;
            $financeiro->valorFaturar = 0;
            $financeiro->valorAberto = 0;
            $financeiro->status = 'aberto';

            $financeiro->save();
            $servico->financeiro = $financeiro;
        
       }
       
       $solicitantes = Solicitante::whereHas('empresas',function($q) use($servico){
        $q->where('empresa_id', '=', $servico->unidade->empresa_id);
    })->orderBy('nome')->get()->pluck('nome','id');   
       
       
        
        return view('admin.editar-servico')
                    ->with([
                        'servico'=>$servico,
                        'dados'=>$dados,
                        'route'=>$route,
                        'users'=>$users,
                        // 'servico_lpu'=>$servico_lpu,
                        'financeiro'=>$servico->financeiro,
                        'ps'=>$servico->tipo,
                        'solicitantes'=>$solicitantes,
                        'licenciamento'=>$servico->licenciamento,
                                                
                    ]);
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
        $request->validate([
            'solicitante' => 'required',
            'valorTotal' => 'required',           
            
        ]);


        //
        $servico = Servico::find($id);
        $servico->tipo  =   $request->tipo;
        $servico->os    =   $request->os;
        $servico->nome  =   $request->nome;
        $servico->situacao  =  $request->situacao;
        $servico->responsavel_id = $request->responsavel_id;
        $servico->coresponsavel_id = $request->coresponsavel_id;

        
        if($request->licenciamento)
        {
            $servico->licenciamento  =   $request->licenciamento;

        }
        
        $servico->protocolo_numero  =   $request->protocolo_numero;
        $servico->laudo_numero = $request->laudo_numero;

        $servico->observacoes   = $request->observacoes;
        $servico->escopo   = $request->escopo;
        $servico->proposta   = $request->proposta;
        $servico->solicitante = $request->solicitante;
        $servico->departamento = $request->departamento;
        $servico->servico_lpu = $request->servico_lpu;
        $servico->tipoLicenca = $request->tipoLicenca;

        $servico->nf = $request->nf;



        //Edit Financeiro

        
        $financeiro = ServicoFinanceiro::find($servico->financeiro->id);

        $financeiro->valorTotal = $request->valorTotal;
        $financeiro->valorAberto = $request->valorAberto;

        
        $financeiro->save();

       
        if(!$financeiro->wasRecentlyCreated)
                    {   
                        $changes = $financeiro->getChanges();
                        
                        foreach ($changes as $value => $key) {

                           
                            $history = new Historico();
                            $history->servico_id = $servico->id;
                            $history->user_id = Auth::id();
                            $history->observacoes = 'Alterou '.$value.' para R$'.$key.'';
                            $history->created_at = Carbon::now('america/sao_paulo');
                            $history->save();
                        }
                    }
        
        //Edit Faturamento
        

        


        //-----------------------------------

        // Se informou o arquivo, retorna um boolean
        if ($request->hasFile('licenca_anexo') && $request->file('licenca_anexo')->isValid()) {
                $nameFile = null;
                $name = uniqid(date('HisYmd'));
                $extension = $request->licenca_anexo->extension();
                $nameFile = "{$name}.{$extension}";
                // Faz o upload:
                $upload = $request->licenca_anexo->storeAs('licencas', $nameFile);
                // Se tiver funcionado o arquivo foi armazenado em storage/app/public/categories/nomedinamicoarquivo.extensao

                $servico->licenca_anexo = $upload;

            }

         // Se informou o arquivo, retorna um boolean
        if ($request->hasFile('protocolo_anexo') && $request->file('protocolo_anexo')->isValid()) {
                $nameFile = null;
                $name = uniqid(date('HisYmd'));
                $extension = $request->protocolo_anexo->extension();
                $nameFile = "{$name}.{$extension}";
                // Faz o upload:
                $upload = $request->protocolo_anexo->storeAs('protocolos', $nameFile);
                // Se tiver funcionado o arquivo foi armazenado em storage/app/public/categories/nomedinamicoarquivo.extensao

                $servico->protocolo_anexo = $upload;


            }

          // Se informou o arquivo, retorna um boolean
        if ($request->hasFile('laudo_anexo') && $request->file('laudo_anexo')->isValid()) {
                $nameFile = null;
                $name = uniqid(date('HisYmd'));
                $extension = $request->laudo_anexo->extension();
                $nameFile = "{$name}.{$extension}";
                // Faz o upload:
                $upload = $request->laudo_anexo->storeAs('laudos', $nameFile);
                // Se tiver funcionado o arquivo foi armazenado em storage/app/public/categories/nomedinamicoarquivo.extensao

                $servico->laudo_anexo = $upload;


            }




       if($request->protocolo_emissao)
        {
            $servico->protocolo_emissao = Carbon::createFromFormat('d/m/Y', $request->protocolo_emissao)->toDateString();
        }

        if($request->licenca_emissao)
        {
            $servico->licenca_emissao = Carbon::createFromFormat('d/m/Y', $request->licenca_emissao)->toDateString();
        }
        if($request->licenca_validade)
        {
            $servico->licenca_validade = Carbon::createFromFormat('d/m/Y', $request->licenca_validade)->toDateString();
        }

        if($request->licenca_validade == '')


        {
            $servico->licenca_validade = $request->licenca_validade;
        }

        if($request->laudo_emissao)
        {
            $servico->laudo_emissao = Carbon::createFromFormat('d/m/Y', $request->laudo_emissao)->toDateString();
        }

       
        

        if($servico->tipoLicenca == 'n/a' || $servico->tipoLicenca == 'definitiva')
        {
            
            $servico->licenca_validade = '2059-12-31';
        }

        
     
        $servico->save();

        
        if (!$servico->wasRecentlyCreated) {
            
            $changes = $servico->getChanges();
            unset($changes['updated_at']);

             foreach ($changes as $value => $key) {
                 
                    $history = new Historico();
                    $history->servico_id = $servico->id;
                    $history->user_id = Auth::id();
                    $history->observacoes = 'Alterou '.$value.' para "'.$key.'"';
                    $history->created_at = Carbon::now('america/sao_paulo');
                    $history->save();


                    //Update Servico Finalizado

                    if($history->observacoes == 'Alterou situacao para "finalizado"')
                    {   
                        if(!ServicoFinalizado::where('servico_id',$servico->id)->first())
                        {
                            $this->finalizarServico($servico->id);
                            $this->removerVinculo($servico->vinculos);
                        }
                        
                    }

                    if($history->observacoes == 'Alterou situacao para "andamento"')
                    {
                        $mS = ServicoFinalizado::where('servico_id',$servico->id)->delete();
                        
                    }

                    
             }
        }


          
        

      

        return redirect()->route('servicos.show',$servico->id);


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
        // $servico = Servico::delete($id);
        // return redirect()->route('servicos.index');

        return $id;
    }

    public function delete($id)
    {

        $servico = Servico::find($id);
        $servico->pendencias()->delete();
        $servico->taxas()->delete();
        $servico->vinculos()->delete();
        $servico->servicoFinalizado()->delete();
        $servico->delete();


        //Insert history

        $history = new Historico();
        $history->servico_id = $servico->id;
        $history->user_id = Auth::id();
        $history->observacoes = "Serviço ".$servico->id." excluido.";
        $history->created_at = Carbon::now('america/sao_paulo');
        $history->save();




        return redirect()->route('unidades.show',$servico->unidade_id);
    }


    public function salvarInteracao(Request $request)
    {   
        $validator = Validator::make($request->all(), [
            'observacoes' => 'required',
        ])->validate();
    
        // Create a new Historico instance
        $interacao = new Historico;
        $interacao->servico_id = $request->servico_id;
        $interacao->observacoes = $request->observacoes;
        $interacao->user_id = Auth::id();
        $interacao->created_at = Carbon::now('America/Sao_Paulo');
        $interacao->save();
    
        // Find the associated service
        $servico = Servico::find($request->servico_id);
    
        // Check for mentions in the observacoes
        $mentions = preg_match_all('[\B@[a-zA-Z\wÀ-ú]+\s\w+]', $request->observacoes, $users);
        if ($mentions > 0) {
            foreach ($users[0] as $u) {
                $u = ltrim($u, "@");
                $user = User::where('name', 'like', '%' . $u . '%')->first();
                if ($user) {
                    $route = $user->privileges == 'admin' ? 'servicos.show' : 'cliente.servico.show';
                    Notification::send($user, new UserMentioned($interacao->servico_id, $route));
                    Mail::to($user)->send(new UsuarioMencionado($servico, $route));
                }
            }
        }
    
        // Redirect to the show route for the service
        return redirect()->route('servicos.show', $request->servico_id);
    }
    


    public function interacoes($id)
    {
        $interacoes = $this->interacoesAutomaticas($id);

        $interacoesSistema = Historico::where('servico_id',$id)->whereNotIn('id',$interacoes->pluck('id'))->get();



        $servico = Servico::select('os','id')->find($id);
        

        return view('admin.lista-interacoes')->with(
            [
                'interacoes'=>$interacoes,
                'interacoesSistema' => $interacoesSistema,
                'servico'=>$servico,
                
                ]);
    }

    public function interacoesAutomaticas($id){

	
        $filters = [
            'observacoes' => [
                'Serviço '.$id.' cadastrado',
                'Alterou ',
                'Concluiu ',
                'Pendencia ',
                'Taxa ',
                'Marcou ',
            ],
        ];
        
        $historico = \App\Models\Historico::where('servico_id',$id)
                    ->where(function($query) use ($filters, $id) {
                        $ids = \App\Models\Historico::where('servico_id',$id)->filter($filters)->pluck('id');
                        $query->whereNotIn('id', $ids);
                    })
                    ->get();
                    
        return $historico;
    }


    public function timeline($id)
    {
        $servico = Servico::find($id);

	
	    $pendencias = Pendencia::whereIn('id',$servico->pendencias->pluck('id'))
										->get()
										->groupBy(function($data){
													return Carbon::parse($data->created_at)->format('Y-m-d');
												});


												

	
	return view('admin.timeline')->with('servico',$servico);
    }


    public function getUnidadesList()
    {
        $unidadesList = Unidade::where('empresa_id', UserAccess::where('user_id',Auth::id())->pluck('empresa_id'))->pluck('id');

        return $unidadesList;
    }


    public function removerProtocolo($id)
    {
        $servico = Servico::find($id);
        $servico->protocolo_anexo = null;
        $servico->save();
    }

    public function removerLicenca($id)
    {
        $servico = Servico::find($id);
        $servico->licenca_anexo = null;
        $servico->save();
    }

    public function removerLaudo($id)
    {
        $servico = Servico::find($id);
        $servico->laudo_anexo = null;
        $servico->save();
    }


    public function finalizarServico($id)
    {
        $servico = new ServicoFinalizado;
        $servico->servico_id = $id;
        $servico->finalizado = date('Y-m-d');
        $servico->save();
    }

    public function removerVinculo($vinculos)
    {
       
       
       //find pendencia

       foreach($vinculos as $v)
       {
           $pendencia = Pendencia::find($v->pendencia_id);
           $pendencia->vencimento = date('Y-m-d');
           $pendencia->save();

           //remove vinculo

           $pendencia_vinculo = PendenciasVinculos::where('id',$v->id)->delete();
       }
               
        
    }


    public function findFirstNum($myString) {

        $slength = strlen($myString);
    
        for ($index = 0;  $index < $slength; $index++)
        {
            $char = substr($myString, $index, 1);
    
            if (is_numeric($char))
            {
                return $index;
            }
        }
    
        return 0;  //no numbers found
    }


    public function anexarLaudo(Request $request)
    {
        $servico = Servico::find($request->servico_id);

        if($request->laudo_emissao)
        {
            $servico->laudo_emissao = Carbon::createFromFormat('d/m/Y', $request->laudo_emissao)->toDateString();
        }
        
        $servico->laudo_numero  =   $request->laudo_numero;

       
        // Se informou o arquivo, retorna um boolean
        if ($request->hasFile('laudo_anexo') && $request->file('laudo_anexo')->isValid()) {
            $nameFile = null;
            $name = uniqid(date('HisYmd'));
            $extension = $request->laudo_anexo->extension();

            $nameFile = "{$name}.{$extension}";
            // Faz o upload:
            $upload = $request->laudo_anexo->storeAs('laudos', $nameFile);
            // Se tiver funcionado o arquivo foi armazenado em storage/app/public/categories/nomedinamicoarquivo.extensao

            $servico->laudo_anexo = $upload;


        }


        $servico->save();

        return redirect()->route('servicos.show',$servico->id);

    }
    
    public function anexarProtocolo(Request $request)
{
    // Retrieve the service by ID from the request
    $servico = Servico::find($request->servico_id);

    // If the request has a protocol emission date, format it
    if($request->protocolo_emissao)
    {
        $servico->protocolo_emissao = Carbon::createFromFormat('d/m/Y', $request->protocolo_emissao)->toDateString();
    }
    
    // Update the service with the protocol number from the request
    $servico->protocolo_numero  =   $request->protocolo_numero;

    // If the request has a valid file, upload it
    if ($request->hasFile('protocolo_anexo') && $request->file('protocolo_anexo')->isValid()) {
        // Generate a unique file name
        $nameFile = null;
        $name = uniqid(date('HisYmd'));
        $extension = $request->protocolo_anexo->extension();

        $nameFile = "{$name}.{$extension}";

        // Upload the file
        $upload = $request->protocolo_anexo->storeAs('protocolos', $nameFile);

        // Save the file path in the service
        $servico->protocolo_anexo = $upload;
    }

    // Save the updated service
    $servico->save();

    // Redirect to the service show page
    return redirect()->route('servicos.show',$servico->id);
}

    
}
