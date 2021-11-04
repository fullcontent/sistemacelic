<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Arquivo;
use App\Models\Historico;
use Illuminate\Support\Facades\Storage;
use Auth;
use App\User;

use App\Models\Pendencia;
use App\Models\Servico;




class ArquivosController extends Controller
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
    public function create()
    {
        //
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

        $a = new Arquivo;

        if ($request->hasFile('arquivo') && $request->file('arquivo')->isValid()) {
                $nameFile = null;
                $name = uniqid(date('HisYmd'));
                
                $extension = $request->arquivo->getClientOriginalExtension();
             
                $nameFile = "{$name}.{$extension}";

                
                $upload = $request->arquivo->storeAs('arquivos', $nameFile);
                // Se tiver funcionado o arquivo foi armazenado em storage/app/public/categories/nomedinamicoarquivo.extensao

                $a->arquivo = $upload;

        }

        $a->nome = $request->nome;

        

        if($request->empresa_id)
        {
            $route = 'empresas.show';
            $id = $request->empresa_id;
            $a->empresa_id = $request->empresa_id;
        }

        if($request->unidade_id)
        {
            $route = 'unidades.show';
            $id = $request->unidade_id;
            $a->unidade_id = $request->unidade_id;
        }

        if($request->servico_id)
        {
            $route = 'servicos.show';
            $id = $request->servico_id;
            $a->servico_id = $request->servico_id;
        }

        if($request->pendencia_id)
        {
            $route = 'servicos.show';
            $id = $request->servico_id;
            $a->pendencia_id = $request->pendencia_id;
        }

        $a->user_id = $request->user_id;

        
                
        
        

        // return $a;
 
        $a->save();

        
        return redirect()->route($route,$id);

        
    }

    public function anexar(Request $request)
    {
        $a = new Arquivo;

        if ($request->hasFile('arquivo') && $request->file('arquivo')->isValid()) {
                $nameFile = null;
                $name = uniqid(date('HisYmd'));
                $extension = $request->arquivo->extension();
                $nameFile = "{$name}.{$extension}";
                // Faz o upload:
                $upload = $request->arquivo->storeAs('arquivos', $nameFile);
                // Se tiver funcionado o arquivo foi armazenado em storage/app/public/categories/nomedinamicoarquivo.extensao

                $a->arquivo = $upload;

        }

        $a->nome = $request->nome;
        $a->user_id = $request->user_id;
        $a->pendencia_id = $request->pendencia_id;
        $a->servico_id = $request->servico_id;
        $a->unidade_id = $request->unidade_id;

        $a->save();



        //Alterar responsavel pela pendência 

        $pendencia = Pendencia::find($request->pendencia_id);
        $servico = Servico::find($pendencia->servico_id);

        
        $pendencia->responsavel_id = $servico->responsavel_id;
        $pendencia->responsavel_tipo = 'usuario';
        
        $nome = $pendencia->pendencia;
        $anexo = "[CLIENTE] ";

        $pendencia->pendencia = $anexo;
        $pendencia->pendencia .= $nome;
        

        $pendencia->save();

        //===================================================
        
        
        //Salvar historico


        $this->salvarHistorico($servico, $pendencia);
        
        
        
        //Notificar o usuario responsavel pelo serviço

        


        //===================================================
        

        //Redirecionar para a pagina do serviço


        $user = User::find($request->user_id);


        if($user->privileges == 'cliente')
        {

            return redirect()->route('cliente.servico.show',$request->servico_id);

        }

        else
        {
            return redirect()->route('servico',$request->servico_id);
        }
        
        



    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    

    public function download($id)
    {
        
        $file = Arquivo::find($id);

        $filename = $file->arquivo;
        
        $extension = pathinfo($filename, PATHINFO_EXTENSION);
        
        $arquivo = $file->unidade->codigo.' - '.$file->unidade->nomeFantasia.' - '.$file->nome.'.'.$extension;

        

                
        return response()->download(public_path('uploads/'.$file->arquivo.''),$arquivo);

    }

    public function downloadFile($tipo, $servico_id)
    {
        

        $servico = Servico::find($servico_id);

        switch ($tipo) {
            case 'licenca':
                $filename = $servico->licenca_anexo;
                $tipo = "Licença";
                break;
                case 'laudo':
                    $filename = $servico->laudo_anexo;
                    $tipo = "Laudo";
                    break;
                    case 'protocolo':
                        $filename = $servico->protocolo_anexo;
                        $tipo = "Protocolo";
                        break;
            
        }

                       
                
        $extension = pathinfo($filename, PATHINFO_EXTENSION);
        
        $arquivo = $tipo.' '.$servico->unidade->codigo.' - '.$servico->unidade->nomeFantasia.' - '.$servico->nome.'.'.$extension;

        $headers = [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'inline; filename="'.$arquivo.'"',


        ];
                       
        return response()->file(public_path('uploads/'.$filename.''),$headers);

    }

    public function delete($id)
    {
        $file = Arquivo::find($id);

        $delete = Storage::delete($file->arquivo);

        $file->delete();



        return redirect()->back();
    }


    public function salvarHistorico($servico, $pendencia)
    {
         //Insert history

        $history = new Historico();
        $history->servico_id = $servico->id;
        $history->user_id = Auth::id();
        $history->observacoes = "Anexou documento ".$pendencia->pendencia." ";
        $history->save();

    }
}
