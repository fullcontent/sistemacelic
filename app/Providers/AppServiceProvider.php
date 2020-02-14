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
                            'text' => 'Empresas',
                            'url' =>  ''.Auth::user()->privileges.'/empresas',
                            'icon' => 'glyphicon glyphicon-briefcase'
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
                            'text' => 'Finalizados',
                            'url'  =>  route('servico.finalizado'),
                            'icon'  =>  'glyphicon glyphicon-ok-sign text-green',
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
                            ],
                            [
                            'text' => 'Listagem geral',
                            'url'  =>  ''.Auth::user()->privileges.'/servicos',
                            'icon'  =>  'glyphicon glyphicon-th-list',
                            ],
                            ]
                            ]


                            );
                            $event->menu->add(
                            ['header'=> 'Administração'],
                            [
                            'text' => 'Usuários',
                            'url'  =>  ''.Auth::user()->privileges.'/usuarios',
                            'icon' => 'fa fa-users'
                            ]
                            );
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
