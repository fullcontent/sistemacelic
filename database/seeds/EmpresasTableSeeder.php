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
       $empresa = factory(App\Models\Empresa::class,4)
                ->create();
        
    }
}
