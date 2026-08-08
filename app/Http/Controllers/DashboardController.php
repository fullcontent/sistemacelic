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

    public function getLicencasEmitidasMes() {
               
        $licencasEmitidas = ServicoFinalizado::with('servico')
           ->whereHas('servico', function($query) {
               $query->where('tipo', 'licencaOperacao');
           })
           ->get()
           ->groupBy(function($item) {
             return $item->finalizado->format('m-Y');
           });
      
        $output = [];
        foreach ($licencasEmitidas as $key => $licencasEmitidas){
          $month = $key;
          $totalCount = $licencasEmitidas->count();
          $output[] = ['month' => $month, 'total_count' => $totalCount];
        }
      
        return response()->json($output);
      }
      


    public function getTotalServicosFinalizadosMes($unidade_id) {
        $s=Servico::where('unidade_id', $unidade_id)->pluck('id');

        $sFinalizados=ServicoFinalizado::whereIn('servico_id', $s) ->get() ->groupBy(function($item) {
                return $item->finalizado->format('m-Y');
            }

        );

        $data=[];

        foreach($sFinalizados as $key=> $sFinalizados) {
            $data[]=[ "month"=>$key,
            "totalCount"=>$sFinalizados->count()];
        }

        return response()->json($data);
    }

    

    public function getServicosUsuarioByStatus($user)
    {
      
        $sU = Servico::porUsuario($user)->get()->groupBy('situacao');

        foreach($sU as $key => $sU){
            $situacao = $key;
            $totalCount = $sU->count();
            dump('Situacao: '.$situacao.' Qtde: '.$totalCount);
        }
                           
    }




    public function getOurClientsLocation()
    {
      
       //Where is our clients

       $cidades = Unidade::with('empresa')->where('status','ativa')->whereNull('latitude')->whereNull('longitude')->get();

       $cities = [];
       foreach($cidades as $key => $c)
       {
            $cities[$key] = [
                'endereco'=>$c->endereco,
                'numero'=>$c->numero,
                'cidade' => $c->cidade,
                'uf'=>$c->uf,
                'unidade_id'=>$c->id,
            ];
            
       }

       $markers = [];

       foreach($cities as $city)
       {

           $mark = $this->getGeoCode($city['endereco'].",".$city['numero']." ".$city['cidade']." ".$city['uf']);
           

           $unidade = Unidade::find($city['unidade_id']);

           $unidade->latitude = $mark['latitude'];
           $unidade->longitude = $mark['longitude'];
           $unidade->save();

           
       }

    //    return response()->json($markers,200);
       
       
                          
    }


    public static function getGeoCode($address)
    {
        $apiKey = config('services.google_maps.api_key') ?: env('GOOGLE_MAPS_API_KEY');
        if (!$apiKey) {
            return null;
        }

        $fullAddress = $address;
        if (!preg_match('/brasil|brazil/i', $fullAddress)) {
            $fullAddress .= ', Brasil';
        }

        $url = 'https://maps.googleapis.com/maps/api/geocode/json?' . http_build_query([
            'address' => $fullAddress,
            'key' => $apiKey,
        ]);

        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 10);
        $json = curl_exec($ch);
        curl_close($ch);

        $result = json_decode($json, true);

        if (!empty($result['results'][0]['geometry']['location'])) {
            $lat = (float) $result['results'][0]['geometry']['location']['lat'];
            $lng = (float) $result['results'][0]['geometry']['location']['lng'];

            // Bounding box check for Brazil: lat [-33.75, 5.27], lng [-73.99, -34.79]
            if ($lat >= -33.75 && $lat <= 5.27 && $lng >= -73.99 && $lng <= -34.79) {
                return [
                    'latitude' => $lat,
                    'longitude' => $lng,
                ];
            }
        }

        return null;
    }


    public function mapa()
    {   

        $unidades = Unidade::whereNotNull('latitude')->whereNotNull('longitude')->with('empresa')->get();
        $unidadesByState = $this->getUnidadesByState();
        
        return view('admin.dashboard.mapa')->with(compact('unidades','unidadesByState'));
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


    public static function getUserServicesCount($user_id)
    {
        return Servico::porUsuario($user_id)->count();

        // dump("Total de Servicos: ".Servico::where('responsavel_id',$user_id)->count());
        // dump("Total de Servicos Finalizados: ".Servico::where('responsavel_id',$user_id)->whereHas('servicoFinalizado')->count());
        // dump("Total de Servicos Arquivados: ".Servico::where('responsavel_id',$user_id)->where('situacao','arquivado')->count());
        // dump("Total de Servicos Andamento: ".Servico::where('responsavel_id',$user_id)->where('situacao','andamento')->count());

        
    }


    public function getTotalOfBoletos()
    {

    }

    public function getTotalOfComprovantes()
    {
        # code...
    }

    public function getUnidadesByState()
        {
            $unidades = Unidade::selectRaw('uf, count(*) as total')
                            ->groupBy('uf')
                            ->get()
                            ->sortBy('total');

            return response()->json($unidades);
        }

    public function getUnidadesByRegion(){
            
        $unidades = Unidade::all();

        $regions = [    
            'Norte' => ['AC', 'AM', 'AP', 'PA', 'RO', 'RR', 'TO'],
            'Nordeste' => ['AL', 'BA', 'CE', 'MA', 'PB', 'PE', 'PI', 'RN', 'SE'],
            'Centro-Oeste' => ['DF', 'GO', 'MT', 'MS'],
            'Sudeste' => ['ES', 'MG', 'RJ', 'SP'],
            'Sul' => ['PR', 'RS', 'SC']
        ];

        $grouped = [];
        foreach ($regions as $region => $states) {
            $grouped[$region]['total'] = 0;
            foreach ($states as $state) {
                $grouped[$region]['total'] += $unidades->where('uf', $state)->count();
            }
        }

        return response()->json($grouped);
    } 


    public function usersMoreActive() {
        $historico = Historico::with('user')
            ->select('user_id', DB::raw('count(*) as total'))
            ->groupBy('user_id')
            ->where('user_id','!=',null)
            ->where('user_id','!=',1)
            ->get();

        
    
        $historicoWithPercentage = [];
        $totalCount = Historico::count();
    
        foreach ($historico as $item) {
            $percentage = ($item->total / $totalCount) * 100;
            $historicoWithPercentage[] = [
                'user_id' => $item->user_id,
                'user_name' => $item->user->name,
                'total' => $item->total,
                'percentage' => $percentage
            ];
        }
    
        return response()->json($historicoWithPercentage);
    }

    



}
