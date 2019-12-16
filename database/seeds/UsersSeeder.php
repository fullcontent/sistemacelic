<?php

use Illuminate\Database\Seeder;

class UsersSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('users')->insert([
            'name' => 'Bruno Carvalho',
            'email' => 'bgc1988@gmail.com',
            'password' => bcrypt('juc4b4l4'),
            'privileges' => 'admin',
            ]);

        DB::table('users')->insert([
            'name' => 'Diego Castro',
            'email' => 'diego@celic.com',
            'password' => bcrypt('123456'),
            'privileges' => 'admin',
            ]);

        $users = factory(App\User::class,10)
                ->create();
    }
}