<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Faker\Generator as FakerGenerator;
use Faker\Factory as FakerFactory;

use Illuminate\Contracts\Events\Dispatcher;
use JeroenNoten\LaravelAdminLte\Events\BuildingMenu;
use Auth;




class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
        $this->app->singleton(FakerGenerator::class, function () {
            return FakerFactory::create('pt_BR');
        });
    }



    /**
     * Bootstrap any application services.
     *
     * @return void
     */
   public function boot(Dispatcher $events)
    {

        
        
            \Carbon\Carbon::setlocale(LC_TIME, 'pt-BR');



        $events->listen(BuildingMenu::class, function (BuildingMenu $event) {
                

                        




                        

                            if(Auth::user()->privileges == 'admin')
                            {
                            $event->menu->add(
                            ['header' => 'main_navigation'],
                            [
                            'text' => 'Dashboard',
                            'url' =>  ''.Auth::user()->privileges.'/home',
                            'icon' => 'glyphicon glyphicon-home'
                            ],
                            [
                                'text' => 'Relatorios',
                                'url' =>  ''.Auth::user()->privileges.'/relatorios',
                                'icon' => 'glyphicon glyphicon-object-align-bottom
                                '
                                ],
                            [
                            'text' => 'Empresas',
                            'url' =>  ''.Auth::user()->privileges.'/empresas',
                            'icon' => 'glyphicon glyphicon-briefcase',
                            
                            ],
                            [
                            'text' => 'Unidades',
                            'url'  =>  ''.Auth::user()->privileges.'/unidades',
                            'icon' => 'glyphicon glyphicon-align-justify'
                            ],
                            [
                            'text' => 'Serviços',
                            'url'  =>  ''.Auth::user()->privileges.'/servicos',
                            'icon' => 'glyphicon glyphicon-wrench',
                            'submenu'=>[
                            [
                            'text'=>'Todos os serviços',
                            'url'  =>  route('servico.lista'),
                            'icon'=>'glyphicon glyphicon-wrench',
                            ],
                            [
                            'text' => 'Em andamento',
                            'url'  =>  route('servico.andamento'),
                            'icon'  =>  'glyphicon glyphicon-object-align-left text-yellow',
                            ],
                            [
                            'text' => 'A vencer',
                            'url'  =>  route('servico.vencer'),
                            'icon'  =>  'glyphicon glyphicon-time text-yellow',
                            ],
                            [
                                'text' => 'Não renovados',
                                'url'  =>  route('servico.nRenovado'),
                                'icon'  =>  'glyphicon glyphicon-remove text-yellow',
                                ],
                            [
                            'text' => 'Finalizados',
                            'url'  =>  route('servico.finalizado'),
                            'icon'  =>  'glyphicon glyphicon-ok-sign text-green',
                            ],
                            [
                            'text' => 'Arquivados',
                            'url'  =>  route('servico.arquivado'),
                            'icon'  =>  'glyphicon glyphicon-inbox',
                            ],
                            [
                            'text' => 'Vigentes',
                            'url'  =>  route('servico.vigente'),
                            'icon'  =>  'glyphicon glyphicon-tags text-aqua',
                            ],
                            [
                            'text' => 'Vencidos',
                            'url'  =>  route('servico.vencido'),
                            'icon'  =>  'glyphicon glyphicon-remove-sign text-red',
                            ],
                            [
                            'text' => 'Unid. inativas',
                            'url'  =>  route('servico.inativo'),
                            'icon'  =>  'glyphicon glyphicon-ban-circle text-red',
                            ]
                            
                            ]
                        ],
                            [
                            'text' => 'Pendências',
                            'url' => ''.Auth::user()->privileges.'/pendencias',
                            'icon' => 'fa fa-tasks',
                            'submenu'=>[
                            [
                            'text'=>'Minhas pendências',
                            'url' => route('pendencias.minhas'),
                            'icon'=>'fa fa-user-secret',
                            ],
                            [
                            'text' => 'Outras pendências',
                            'url' => route('pendencias.outras'),
                            'icon' => 'fa fa-child',
                            ],
                            [
                            'text' => 'Pendências Vinculadas',
                            'url' => route('pendencias.vinculadas'),
                            'icon' => 'fa fa-link',
                            ],

                            ]
                            ],
                            [
                            'text' => 'Propostas',
                            'url' => ''.Auth::user()->privileges.'/proposta',
                            'icon' => 'fa fa-newspaper',
                            ],
                            [
                                'text' => 'Ordens de Compra',
                                'url' => ''.Auth::user()->privileges.'/ordemCompra',
                                'icon' => 'fa fa-credit-card',
                                ],
                                [
                                    'text' => 'Prestadores',
                                    'url' => ''.Auth::user()->privileges.'/prestador',
                                    'icon' => 'fa fa-user',
                                    ],
                            


                            );

                            if(Auth::id() <= 3)
                            {
                                $event->menu->add(
                                    ['header'=> 'Administração'],
                                    [
                                    'text' => 'Usuários',
                                    'url'  =>  ''.Auth::user()->privileges.'/usuarios',
                                    'icon' => 'fa fa-users'
                                    ],
                                    [
                                        'text'=>'Solicitantes',
                                        'url' => ''.Auth::user()->privileges.'/solicitantes',
                                        'icon' => 'glyphicon glyphicon-user'
                                    ],
                                    [
                                        'text' => 'Listagem geral dos serviços',
                                        'url'  =>  ''.Auth::user()->privileges.'/servicos',
                                        'icon'  =>  'glyphicon glyphicon-th-list',
                                    ],
                                    [
                                        'text' => 'Faturamentos',
                                        'url'  =>  ''.Auth::user()->privileges.'/faturamentos',
                                        'icon'  =>  'glyphicon glyphicon glyphicon-barcode',
                                    ],
                                    [
                                        'text' => 'Reembolsos',
                                        'url'  =>  ''.Auth::user()->privileges.'/reembolsos',
                                        'icon'  =>  'glyphicon glyphicon glyphicon-usd',
                                    ]
                                    
                                );
                            }
                            elseif(Auth::id() != 15 || Auth::id() != 21 || Auth::id() != 14 || Auth::id() != 8 || Auth::id() != 27)
                            {
                                $event->menu->add(
                                    ['header'=> 'Administração'],
                                    [
                                        'text' => 'Relatório Completo de Serviços',
                                        'url'  =>  ''.Auth::user()->privileges.'/relatorio',
                                        'icon'  =>  'glyphicon glyphicon glyphicon-th-list',
                                    ]
                                    
                                );
                            }
                            
                            }

                        if(Auth::user()->privileges == 'cliente')

                        {
                            $event->menu->add(
                            ['header'=> 'Cliente'],

                            //AQUI VAI O MENU DO CLIENTE
                            [
                            'text' => 'Dashboard',
                            'url' =>  route('cliente.home'),
                            'icon' => 'glyphicon glyphicon-home'
                            ],
                            [
                                'text' => 'Relatorio',
                                'url' =>  route('cliente.servicos'),
                                'icon' => 'glyphicon glyphicon-object-align-bottom
                                '
                                ],
                            [
                            'text' => 'Empresas',
                            'url' =>  route('cliente.empresas'),
                            'icon' => 'glyphicon glyphicon-briefcase'
                            ],
                            [
                            'text' => 'Unidades',
                            'url'  =>  route('cliente.unidades'),
                            'icon' => 'glyphicon glyphicon-align-justify'
                            ],
                            [
                            'text' => 'Serviços',
                            'url'  =>  route('cliente.servicos'),
                            'icon' => 'glyphicon glyphicon-wrench',
                            'submenu'=>[
                                [
                                'text'=>'Todos os serviços',
                                'url'  =>  route('cliente.servicos'),
                                'icon'=>'glyphicon glyphicon-wrench',
                                ],
                                [
                            'text' => 'Em andamento',
                            'url'  =>  route('cliente.servico.andamento'),
                            'icon'  =>  'glyphicon glyphicon-object-align-left text-yellow',
                            ],
                            [
                            'text' => 'A vencer',
                            'url'  =>  route('cliente.servico.vencer'),
                            'icon'  =>  'glyphicon glyphicon-time text-yellow',
                            ],
                            [
                            'text' => 'Finalizados',
                            'url'  =>  route('cliente.servico.finalizado'),
                            'icon'  =>  'glyphicon glyphicon-ok-sign text-green',
                            ],
                            [
                            'text' => 'Vigentes',
                            'url'  =>  route('cliente.servico.vigente'),
                            'icon'  =>  'glyphicon glyphicon-tags text-aqua',
                            ],
                            [
                            'text' => 'Vencidos',
                            'url'  =>  route('cliente.servico.vencido'),
                            'icon'  =>  'glyphicon glyphicon-remove-sign text-red',
                            ],
                            [
                            'text' => 'Unid. inativas',
                            'url'  =>  route('cliente.servico.inativo'),
                            'icon'  =>  'glyphicon glyphicon-ban-circle text-red',
                            ],
                                
                            ]//EndSubmenu
                            ]



                            );
                        }
                
        });
    }
}
