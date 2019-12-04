<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Models\Historico;
use Faker\Generator as Faker;

$factory->define(Historico::class, function (Faker $faker) {
    return [
        //
        // 'servico_id' => 1,
        'anexo'	=>	Str::random(10),
        'observacoes' => $faker->sentence,
    ];
});
