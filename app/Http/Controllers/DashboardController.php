<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use DB;
use File;

use App\Models\Servico;
use App\Models\ServicoFinalizado;
use App\Models\Pendencia;
use App\Models\Unidade;
use App\Models\Faturamento;
use App\Models\Historico;


class DashboardController extends Controller
{
    
    public function test($user_id)
    {
        
       
        return $this->getUserInteractions($user_id);
        // return $this->getOurClientsLocation();
                 
    }

    public function getLicencasEmitidasMes()
    {
      
        $s = Servico::where('tipo','licencaOperacao')->pluck('id');

        $sFinalizados = ServicoFinalizado::whereIn('servico_id',$s)
                        // ->whereMonth('finalizado',date('m'))
                        ->get()
                        ->groupBy(function($item){
                            return $item->finalizado->format('m-Y');
                        });
        
                       foreach($sFinalizados as $key => $sFinalizados){
                            $month = $key;
                            $totalCount = $sFinalizados->count();
                            dump('Mês: '.$month.' Qtde: '.$totalCount);
                        }
        
                   
    }


    public function getTotalServicosFinalizadosMes($unidade_id)
    {
      
        $s = Servico::where('unidade_id',$unidade_id)->pluck('id');

        $sFinalizados = ServicoFinalizado::whereIn('servico_id',$s)
                        ->get()
                        ->groupBy(function($item){
                            return $item->finalizado->format('m-Y');
                        });
        
                       foreach($sFinalizados as $key => $sFinalizados){
                            $month = $key;
                            $totalCount = $sFinalizados->count();
                            dump('Mês: '.$month.' Qtde: '.$totalCount);
                        }
                   
    }

    public function getServicosUsuarioByStatus($user)
    {
      
        $sU = Servico::where('responsavel_id', $user)->get()->groupBy('situacao');

        foreach($sU as $key => $sU){
            $situacao = $key;
            $totalCount = $sU->count();
            dump('Situacao: '.$situacao.' Qtde: '.$totalCount);
        }
                           
    }




    public function getOurClientsLocation()
    {
      
       //Where is our clients

       $cidades = Unidade::with('empresa')->where('status','ativa')->get();

       $cities = [];
       foreach($cidades as $key => $c)
       {
            $cities[$key] = [
                'endereco'=>$c->endereco,
                'cidade' => $c->cidade,
                'uf'=>$c->uf,
                'empresa'=>$c->empresa->nomeFantasia,
            ];
            
       }
       
    //    dump($cities);
       return view('admin.dashboard.mapa')->with('cidades',$cities);
       
                          
    }


    public function getUserInteractions($user_id)
    {

        //Get by date -> service -> interactions


        $history = Historico::where('user_id',$user_id)
        ->with('servico')
        
        ->paginate(50)
        // ->get()
        ->sortByDesc('created_at')
        ->groupBy(function($i){
            return $i->created_at->format('d-m-Y');
        });

        
        foreach($history as $key => $h)
        {
            $data = $key;

            $servicos = $h->groupBy('servico_id');
            
            // dump($data);
            foreach($servicos as $servico_id => $s)
            {   
                // dump($servico_id);
                foreach($s as $i)
                {
                    // dump($i->observacoes);
                }
            }

                  
        }

        return view('admin.components.widget-user-timeline')->with('interacoes',$history);
        
    }


    public function getUserServicesCount($user_id)
    {
        // return Servico::where('responsavel_id',$user_id)->count();

        dump("Total de Servicos: ".Servico::where('responsavel_id',$user_id)->count());
        dump("Total de Servicos Finalizados: ".Servico::where('responsavel_id',$user_id)->whereHas('servicoFinalizado')->count());
        dump("Total de Servicos Arquivados: ".Servico::where('responsavel_id',$user_id)->where('situacao','arquivado')->count());
        dump("Total de Servicos Andamento: ".Servico::where('responsavel_id',$user_id)->where('situacao','andamento')->count());
    }


}
