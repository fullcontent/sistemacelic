<?php

use Illuminate\Database\Seeder;

class TaxasTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //
        $taxas = factory(App\Models\Taxa::class, 10)->create();

    }
}
