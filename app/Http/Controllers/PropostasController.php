<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\Proposta;
use App\Models\PropostaServico;
use App\Models\Unidade;
use App\Models\Servico;
use App\Models\ServicoLpu;
use App\Models\Historico;
use App\Models\ServicoFinanceiro;
use App\Models\Pendencia;
use Carbon\Carbon;
use Auth;

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

        
        $u = Proposta::pluck('id')->last();
        $ultimaProposta = $u+1;

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
        

       
     
        
        $proposta = new Proposta;

        $proposta->id = $request->proposta_id;

        $proposta->unidade_id = $request->unidade_id;
        $proposta->status = "Revisando";


        

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

                $propostaServico->servicoLpu_id = $s['id'];
    
                $propostaServico->save();
    
                // dump($propostaServico);
            }
            
            
            
            
            if(strlen($key) > 1)
            {
                // dump($key);
                   
                    $propostaServicoSub = new PropostaServico;
                    $propostaServicoSub->servico = $s['nome'];
                    $propostaServicoSub->escopo = $s['escopo'];
                    $propostaServicoSub->valor = $s['valor'];
                    $propostaServicoSub->servicoLpu_id = $s['id'];
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

    public function removerProposta($id)
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

        return response()->json(['success'=>true, 'status'=>200,'id'=>$id]);

        

    }

    
    public function recusar($id)
    {
        $proposta = Proposta::find($id);
        $proposta->status = "Recusada";
        $proposta->save();

        return response()->json(['success'=>true, 'status'=>200,'id'=>$id]);
    }

    public function aprovar($id,$s)
    {

        $proposta = Proposta::find($id);
        $proposta->status = "Aprovada";
        $proposta->save();

        $servicos = array();

        
        
        // //Criar os serviços automaticamente de acordo com a proposta

        if($s==1)
        {
            foreach($proposta->servicos as $key => $s)
            {
                
    
                $servico = new Servico;
                $servico->nome = $s->servico;
                $servico->tipo = $s->servicoLpu->tipoServico;
                $servico->situacao = "andamento";
                $servico->responsavel_id = $proposta->responsavel_id;
                $servico->empresa_id = $proposta->empresa_id;
                $servico->unidade_id = $proposta->unidade_id;
                $servico->solicitante = $proposta->solicitante;
                $servico->escopo = $s->escopo;
                $servico->propostaServico_id = $s->id;
                $servico->proposta_id = $proposta->id;
                
                if($s->servicoPrincipal)
                {
                    $servicoPrincipal = Servico::where('propostaServico_id', $s->servicoPrincipal)->pluck('id')->first();
                    $servico->servicoPrincipal = $servicoPrincipal;
                }


                $servico->os = $this->getLastOs($proposta->unidade_id);

                $servico->save();
                $servicos[$key]=$servico;


                //Inserir financeiro

                        
                $faturamento = new ServicoFinanceiro();
                $faturamento->servico_id = $servico->id;

                $faturamento->valorTotal = $s->valor;
                $faturamento->valorAberto = $s->valor;
                $faturamento->save();  
                
                               
                
                //Salvar historico

                
                $history = new Historico();
                $history->servico_id = $servico->id;
                $history->user_id = Auth::id();
                $history->observacoes = "Serviço ".$servico->id." cadastrado.";
                $history->created_at = Carbon::now('america/sao_paulo');
                $history->save();
                

                //Criar Pendência principal

                $pendencia = new Pendencia;

                $pendencia->created_by = Auth::id();
                $pendencia->servico_id = $servico->id;
                $pendencia->pendencia = "Criar pendências!";
                $pendencia->vencimento = date('Y-m-d');
                $pendencia->prioridade = 1;

               
                $pendencia->responsavel_tipo = "usuario";
                $pendencia->responsavel_id = $proposta->responsavel_id;
                $pendencia->status = "pendente";
                $pendencia->observacoes = "Pendência criada automaticamente. Lembrar de criar pendências para esse serviço.";
                

                $pendencia->save();

                    
    
            }
    
    
            
        }

        
        return response()->json(['success'=>true, 'status'=>200,'id'=>$id,'servicos'=>$servicos]);


 

    }

    public function getLastOs($unidade_id)
    {
                    $u = Unidade::find($unidade_id);
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

                    return $os;
    }
}

