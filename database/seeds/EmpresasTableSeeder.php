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
        // factory(App\Models\Empresa::class, 5)->create()->each(function ($unidade) {
        //         $unidade->empresa()->save(factory(App\Models\Empresa::class)->make());


        $empresa = factory(App\Models\Empresa::class)
                    ->create()
                    ->each(function($unidade){
                        $unidade->unidades()->createMany(factory(App\Models\Unidade::class,10)->make()->toArray());
                    });
    }
}
