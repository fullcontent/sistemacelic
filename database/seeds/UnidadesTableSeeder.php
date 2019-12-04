<?php

use Illuminate\Database\Seeder;

class UnidadesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
   public function run()
    {
       
       $unidades = factory(App\Models\Unidade::class,10)
                ->create(['empresa_id'=>factory(App\Models\Empresa::class)])

                ->each(function ($unidade){
                   
                    $servico=factory(App\Models\Servico::class,2)->create(
                        [
                            'unidade_id'=>$unidade->id,
                    ])
                        ->each(function ($t)
                        {
                            $taxas = factory(App\Models\Taxa::class,10)->create(['servico_id' => 1]);
                            $t->taxas()->saveMany($taxas);
                        })
                        ->each(function ($h)
                        {
                            $historico = factory(App\Models\Historico::class,10)->create(['servico_id'=>$h->id]);
                            $h->historico()->saveMany($historico);
                        });

                    $unidade->servicos()->saveMany($servico);


                });

    }
}
