<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\Servico;
use App\Models\Taxa;
use App\Models\Historico;
use Carbon\Carbon;
use Auth;



class TaxasController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $r)
    {
        //
        $servicos = Servico::pluck('os','id')->toArray();

        $servico_id = $r->servico_id;
       
        
        return view('admin.cadastro-taxa')->with(['servicos'=>$servicos,'servico_id'=>$servico_id,'s'=>$servico_id]);
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

        
        $taxa = new Taxa;

        $taxa->nome  = $request->nome;
        $taxa->servico_id = $request->servico_id;
        
        if($request->emissao)
        {
            $taxa->emissao = Carbon::createFromFormat('d/m/Y', $request->emissao)->toDateString(); 
        }
       
        if($request->vencimento)
        {
            $taxa->vencimento = Carbon::createFromFormat('d/m/Y', $request->vencimento)->toDateString();
        }
        
        
        if($request->pagamento)
        {
             $taxa->pagamento = Carbon::createFromFormat('d/m/Y', $request->pagamento)->toDateString();
        }
       
        

        $taxa->valor =  str_replace (',', '.', str_replace ('.', '', $request->valor));
        
        
        $taxa->situacao = $request->situacao;

         // Se informou o arquivo, retorna um boolean
        if ($request->hasFile('boleto') && $request->file('boleto')->isValid()) {
                $nameFile = null;
                $name = uniqid(date('HisYmd'));
                $extension = $request->boleto->extension();
                $nameFile = "{$name}.{$extension}";
                // Faz o upload:
                $upload = $request->boleto->storeAs('boletos', $nameFile);
                // Se tiver funcionado o arquivo foi armazenado em storage/app/public/categories/nomedinamicoarquivo.extensao

                $taxa->boleto = $upload;

            }

         // Se informou o arquivo, retorna um boolean
        if ($request->hasFile('comprovante') && $request->file('comprovante')->isValid()) {
                $nameFile = null;
                $name = uniqid(date('HisYmd'));
                $extension = $request->comprovante->extension();
                $nameFile = "{$name}.{$extension}";
                // Faz o upload:
                $upload = $request->comprovante->storeAs('comprovantes', $nameFile);
                // Se tiver funcionado o arquivo foi armazenado em storage/app/public/categories/nomedinamicoarquivo.extensao

                $taxa->comprovante = $upload;


            }

        $taxa->reembolso = $request->reembolso;

        $taxa->observacoes  = $request->observacoes;
        
        
        $taxa->save();


        //insert history

        $history = new Historico();
        $history->servico_id = $request->servico_id;
        $history->user_id = Auth::id();
        $history->observacoes = "Taxa ".$taxa->nome." cadastrada.";
        $history->created_at = Carbon::now('america/sao_paulo');
        $history->save();

        
        
        return redirect()->route('servicos.show',$request->servico_id);
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
        $taxa = Taxa::find($id);
        $servicos = Servico::pluck('os','id')->toArray();

        // $taxa->emissao = Carbon::parse($taxa->emissao)->format('d/m/Y');
        // $taxa->vencimento = Carbon::parse($taxa->vencimento)->format('d/m/Y');
        // $taxa->pagamento = Carbon::parse($taxa->pagamento)->format('d/m/Y');


        return view('admin.editar-taxa')->with(['taxa'=>$taxa,'servicos'=>$servicos]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
        $taxa = Taxa::find($id);

        // $taxa->emissao = Carbon::createFromFormat('d/m/Y', $taxa->emissao);


       

        return view('admin.editar-taxa')->with(['taxa'=>$taxa]);
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
        

        $taxa = Taxa::find($id);


        $taxa->nome  = $request->nome;
        
        
        if($request->emissao)
        {
            $taxa->emissao = Carbon::createFromFormat('d/m/Y', $request->emissao)->toDateString(); 
        }
      
        

        if($request->pagamento)
        {
            $taxa->pagamento = Carbon::createFromFormat('d/m/Y', $request->pagamento)->toDateString();
        }
        else{
            $taxa->pagamento = null;
        }
        if($taxa->pagamento)
        {
             $taxa->pagamento = Carbon::createFromFormat('d/m/Y', $request->pagamento)->toDateString();
        }

        if($request->vencimento)
        {
            $taxa->vencimento = Carbon::createFromFormat('d/m/Y', $request->vencimento)->toDateString(); 
        }
        
        
        
        
        $taxa->valor =  str_replace (',', '.', str_replace ('.', '', $request->valor));
        $taxa->observacoes = $request->observacoes;
        // $taxa->boleto   =   $request->boleto;
        // $taxa->comprovante = $request->comprovante;
        $taxa->situacao = $request->situacao;
        $taxa->reembolso = $request->reembolso;



         // Se informou o arquivo, retorna um boolean
        if ($request->hasFile('boleto') && $request->file('boleto')->isValid()) {
                $nameFile = null;
                $name = uniqid(date('HisYmd'));
                $extension = $request->boleto->extension();
                $nameFile = "{$name}.{$extension}";
                // Faz o upload:
                $upload = $request->boleto->storeAs('boletos', $nameFile);
                // Se tiver funcionado o arquivo foi armazenado em storage/app/public/categories/nomedinamicoarquivo.extensao

                $taxa->boleto = $upload;

            }

         // Se informou o arquivo, retorna um boolean
        if ($request->hasFile('comprovante') && $request->file('comprovante')->isValid()) {
                $nameFile = null;
                $name = uniqid(date('HisYmd'));
                $extension = $request->comprovante->extension();
                $nameFile = "{$name}.{$extension}";
                // Faz o upload:
                $upload = $request->comprovante->storeAs('comprovantes', $nameFile);
                // Se tiver funcionado o arquivo foi armazenado em storage/app/public/categories/nomedinamicoarquivo.extensao

                $taxa->comprovante = $upload;


            }
        
        
               
        
            
        // return $taxa;
        $taxa->save();
        
                  
        
        return redirect()->route('servicos.show',$taxa->servico_id);


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


    }

    public function salvarHistorico()
    {
         //Insert history

        $history = new Historico();
        $history->servico_id = $servico->id;
        $history->user_id = Auth::id();
        $history->observacoes = "Taxa ".$servico->id." cadastrado.";
        $history->created_at = Carbon::now('america/sao_paulo');
        $history->save();

    }


    
    public function markAsRead(Request $request)
    {   

        
        auth()->user()->notifications->where('id',$request->notif_id)->markAsRead();
        return redirect()->back();
    }

    public function delete($id)
    {   

        $t = Taxa::destroy($id);
        
        return redirect()->back();
    }


    public function removerComprovante($id)
    {
        $taxa = Taxa::find($id);

        $taxa->comprovante = null;
        $taxa->pagamento = null;
        $taxa->save();
    }

    public function removerBoleto($id)
    {
        $taxa = Taxa::find($id);

        $taxa->boleto = null;
        $taxa->save();
    }


}
