<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Servico;
use App\Models\Historico;
use App\User;
use Carbon\Carbon;
use App\Models\Empresa;
use App\Models\Unidade;


use Auth;
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
        
        $servicos = Servico::with('unidade','empresa','responsavel')->get();
        // $servicos = Servico::select('os','nome','tipo','responsavel_id','servicos.unidade_id','servicos.empresa_id')
        //             ->join('unidades', 'unidades.id', '=', 'unidade_id')
        //             ->join('empresas', 'empresas.id', '=', 'empresa_id')
                    
        // ->get();

        // return $servicos;

        return view('admin.lista-servicos')
                    ->with('servicos',$servicos);
    }

    public function listaAndamento()
    {
        

        
        $servicos = Servico::with('unidade','empresa','responsavel')->where('situacao','andamento')->get();
        // $servicos = Servico::select('os','nome','tipo','responsavel_id','servicos.unidade_id','servicos.empresa_id')
        //             ->join('unidades', 'unidades.id', '=', 'unidade_id')
        //             ->join('empresas', 'empresas.id', '=', 'empresa_id')
                    
        // ->get();

        // return $servicos;

        return view('admin.lista-servicos')
                    ->with('servicos',$servicos);
    }

    public function listaFinalizados()
    {
        

        
        $servicos = Servico::with('unidade','empresa','responsavel')->where('situacao','finalizado')->get();
        // $servicos = Servico::select('os','nome','tipo','responsavel_id','servicos.unidade_id','servicos.empresa_id')
        //             ->join('unidades', 'unidades.id', '=', 'unidade_id')
        //             ->join('empresas', 'empresas.id', '=', 'empresa_id')
                    
        // ->get();

        // return $servicos;

        return view('admin.lista-servicos')
                    ->with('servicos',$servicos);
    }

    public function listaVigentes()
    {
        
        $servicos = Servico::with('unidade','empresa','responsavel')
                        ->where('licenca_validade','>',date('Y-m-d'))
                        ->get();
        // $servicos = Servico::select('os','nome','tipo','responsavel_id','servicos.unidade_id','servicos.empresa_id')
        //             ->join('unidades', 'unidades.id', '=', 'unidade_id')
        //             ->join('empresas', 'empresas.id', '=', 'empresa_id')
                    
        // ->get();

        // return $servicos;

        return view('admin.lista-servicos')
                    ->with('servicos',$servicos);
    }

    public function listaVencidos()
    {
        
        $servicos = Servico::with('unidade','empresa','responsavel')
                            ->where('licenca_validade','<',date('Y-m-d'))
                            ->get();
        // $servicos = Servico::select('os','nome','tipo','responsavel_id','servicos.unidade_id','servicos.empresa_id')
        //             ->join('unidades', 'unidades.id', '=', 'unidade_id')
        //             ->join('empresas', 'empresas.id', '=', 'empresa_id')
                    
        // ->get();

        // return $servicos;

        return view('admin.lista-servicos')
                    ->with('servicos',$servicos);
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
    
        $id = $request->id;
        $users = User::where('privileges','=','admin')->pluck('name','id')->toArray();

        

        switch ($tipo) {
            case 'unidade':
                // code...
                    $u = Unidade::find($id);
                    $a = $u->empresa->razaoSocial;
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

                break;
            
            
        }

       
         


        return view('admin.cadastro-servico')
                ->with([
                    't'=>$tipo,
                    'id'=>$id,
                    'users'=>$users,
                    'os' => $os,
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
        
        
        
        $servico = new Servico;
        $servico->tipo  =   $request->tipo;
        $servico->os    =   $request->os;
        $servico->nome  =   $request->nome;
        $servico->situacao  =  $request->situacao;
        $servico->responsavel_id = $request->responsavel_id;

        
        $servico->protocolo_numero  =   $request->protocolo_numero;

        
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

        // return $request;


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


        // $servico->licenca_anexo = $request->licenca_anexo;
        // $servico->protocolo_anexo   = $request->protocolo_anexo;

        $servico->observacoes   = $request->observacoes;


        if($request->t == 'unidade')
        {
            $servico->unidade_id = $request->unidade_id;
        }

        if($request->t == 'empresa')
        {
            $servico->unidade_id = $request->empresa_id;
        }
        
        

        $servico->save();
     

        


        //Insert history

        $history = new Historico();
        $history->servico_id = $servico->id;
        $history->user_id = Auth::id();
        $history->observacoes = "Serviço ".$servico->id." cadastrado.";
        $history->save();

        

        return redirect()->route('servicos.show',$servico->id);



        
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
        $servico = Servico::find($id);

       
        //Check if is empresa or unidade

        if($servico->unidade_id){

            $dados = $servico->unidade;
            $route = 'unidades.edit';
        }
        else{
            $dados = $servico->empresa;
            $route = 'empresas.edit';
        }


        return view('admin.detalhe-servico')
                    ->with([
                        'servico'=>$servico,
                        'dados'=>$dados,
                        'route'=>$route,
                        'taxas'=>$servico->taxas,
                        'pendencias'=>$servico->pendencias,
                        'arquivo'=>'servico',
                    ]);
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
        $users = User::where('privileges','=','admin')->pluck('name','id')->toArray();

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
        
        
        return view('admin.editar-servico')
                    ->with([
                        'servico'=>$servico,
                        'dados'=>$dados,
                        'route'=>$route,
                        'users'=>$users,
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
        //
        $servico = Servico::find($id);
        $servico->tipo  =   $request->tipo;
        $servico->os    =   $request->os;
        $servico->nome  =   $request->nome;
        $servico->situacao  =  $request->situacao;
        $servico->responsavel_id = $request->responsavel_id;

        
        $servico->protocolo_numero  =   $request->protocolo_numero;


       
       


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

        $servico->observacoes   = $request->observacoes;
        

        

        $servico->save();

        
        if (!$servico->wasRecentlyCreated) {
            
            $changes = $servico->getChanges();
            unset($changes['updated_at']);


             foreach ($changes as $value => $key) {
                 
                    $history = new Historico();
                    $history->servico_id = $servico->id;
                    $history->user_id = Auth::id();
                    $history->observacoes = 'Alterou '.$value.' para "'.$key.'"';
                    $history->save();
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
        $servico = Servico::destroy($id);
        return redirect()->route('servicos.index');
    }



    public function salvarInteracao(Request $request)
    {   

        $validator = Validator::make($request->all(), [

                'observacoes'=>'required',
                
            ])->validate();

        $interacao = new Historico;

        $interacao->servico_id = $request->servico_id;
        $interacao->observacoes = $request->observacoes;
        $interacao->user_id = Auth::id();

        $interacao->save();

        return redirect()->route('servicos.show', $request->servico_id);
    }


    public function listarInteracoes($id)
    {
        
        return $id;

    }
}
