<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\Proposta;
use App\Models\PropostaServico;
use App\Models\Unidade;

class PropostasController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {   

        $propostas = Proposta::all();

        return view('admin.proposta.lista-propostas')->with('propostas',$propostas);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {   
        $ultimaProposta = Proposta::pluck('proposta')->first();

        return view('admin.proposta.step1')->with('ultimaProposta',$ultimaProposta);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */

    public function step2($proposta)
    {
        
        $propostaServicos = PropostaServico::where('proposta_id',$proposta->id)->get();
        

        return view('admin.proposta.step2')->with(['proposta'=>$proposta,'propostaServicos'=>$propostaServicos]);
    }

    public function step3(Type $var = null)
    {
        # code...
    }


    public function store(Request $request)
    {   
    
     
        $lastOS = Proposta::pluck('proposta')->first();


        $proposta = new Proposta;

        $proposta->unidade_id = $request->unidade_id;
        $proposta->status = "Revisando";

        $proposta->proposta = $lastOS+1;

        $unidade = Unidade::find($request->unidade_id);
        $proposta->empresa_id = $unidade->empresa_id;

        $proposta->responsavel_id = $request->responsavel_id;
        $proposta->solicitante = $request->solicitante;

        $proposta->documentos = $request->documentos;
        $proposta->condicoesGerais = $request->condicoesGerais;
        $proposta->condicoesPagamento = $request->condicoesPagamento;
        $proposta->dadosPagamento = $request->dadosPagamento;

        $proposta->save();

        // dump($proposta);
        
        foreach($request->servico as $key => $s)
        {   
            


            if(strlen($key) <= 1)
            {
                $propostaServico = new PropostaServico;
                $propostaServico->servico = $s['nome'];
                $propostaServico->escopo = $s['escopo'];
                $propostaServico->valor = $s['valor'];
                $propostaServico->posicao = $key;
    
                $propostaServico->proposta_id = $proposta->id;
    
                $propostaServico->save();
    
                // dump($propostaServico);
            }
            
            
            
            
            if(strlen($key) > 1)
            {
                dump($key);
                   
                    $propostaServicoSub = new PropostaServico;
                    $propostaServicoSub->servico = $s['nome'];
                    $propostaServicoSub->escopo = $s['escopo'];
                    $propostaServicoSub->valor = $s['valor'];
                    $propostaServicoSub->posicao = substr($key,-1);
                    $propostaServicoSub->servicoPrincipal = $propostaServico->id;

                    $propostaServicoSub->proposta_id = $proposta->id;

                    $propostaServicoSub->save();

                    // dump($propostaServicoSub);



            }
            
                      
            
            
                       
        }

        


        return redirect(route('proposta.edit',$proposta->id));
        

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
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {   
        
        $proposta = Proposta::with('servicos')->find($id);
          

        return view('admin.proposta.editar-proposta')->with(['proposta'=>$proposta]);
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
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {   

        return "Deletendo";
        // $proposta = Proposta::find($id);
        // $proposta->delete();

        // foreach($proposta->servicos as $s)
        // {
        //     $servico = PropostaServico::find($s->id);
        //     $servico->delete();
        // }

        // return route('admin.proposta.index');


    }


    public function removerServico($id)
    {
        
         $servico = PropostaServico::find($id);

         $servico->delete();
                
    }

    public function analisar($id)
    {
        $proposta = Proposta::find($id);
        $proposta->status = "Em análise";
        $proposta->save();

    }

    
    public function recusar($id)
    {
        $proposta = Proposta::find($id);
        $proposta->status = "Recusada";
        $proposta->save();
    }

    public function aprovar($id)
    {
        $proposta = Proposta::find($id);
        $proposta->status = "Aprovada";
        $proposta->save();

        //Criar os serviços de acordo com a proposta
    }
}
