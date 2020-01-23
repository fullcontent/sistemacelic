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
                        'icon' => 'glyphicon glyphicon-wrench'
                        ]
                        
                        
                    );

                        if(Auth::user()->privileges == 'admin')

                        {
                            $event->menu->add(
                                ['header'=> 'Administração'],
                                [
                                'text' => 'Usuários',
                                'url'  =>  ''.Auth::user()->privileges.'/usuarios',
                                'icon' => 'fa fa-users'
                                ]


                            );
                        }
                
        });
    }
}
