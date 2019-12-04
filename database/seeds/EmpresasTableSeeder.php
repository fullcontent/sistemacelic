<?php

use Illuminate\Database\Seeder;



class EmpresasTableSeeder extends Seeder
{

	
    /**
     * Run the database seeds.
     *
     * @return void
     */

    


    public function run()
    {
       $empresas = factory(App\Models\Empresa::class,4)
                ->create()
                ->each(function ($empresa){
                   
                    $servico=factory(App\Models\Servico::class,2)->create(
                        [
                            'empresa_id' => $empresa->id,
                    ])
                        ->each(function ($t)
                        {
                            $taxas = factory(App\Models\Taxa::class,5)->create(['servico_id' => $t->id]);
                            $t->taxas()->saveMany($taxas);
                        })
                        ->each(function ($h)
                        {
                            $historico = factory(App\Models\Historico::class,10)->create(['servico_id'=>$h->id]);
                            $h->historico()->saveMany($historico);
                        }
                    );

                    $empresa->servicos()->saveMany($servico);


                });
        
    }
}
