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
        factory(App\Models\Unidade::class, 5)->create();

    }
}
