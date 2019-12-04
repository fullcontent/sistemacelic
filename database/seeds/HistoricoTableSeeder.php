<?php

use Illuminate\Database\Seeder;

class HistoricoTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //
        $historico = factory(App\Models\Historico::class, 10)->create();
    }
}
