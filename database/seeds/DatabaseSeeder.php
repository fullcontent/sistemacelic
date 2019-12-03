<?php

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        $this->call(UsersSeeder::class);
        $this->call(EmpresasTableSeeder::class);
        $this->call(UnidadesTableSeeder::class);
        $this->call(ServicosTableSeeder::class);

    }
}
