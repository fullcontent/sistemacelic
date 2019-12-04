<?php

use Illuminate\Database\Seeder;

class ServicosTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //
        $servicos = factory(App\Models\Servico::class,10)->create()
        ->each(function ($servico){
            
            $historico = factory(App\Models\Historico::class,20)->make();
            $servico->historico()->saveMany($historico);

            $taxa = factory(App\Models\Taxa::class,5)->make();
            $servico->taxas()->saveMany($taxa);
        });

    }
}
