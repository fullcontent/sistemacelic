<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Models\Taxa;
use Faker\Generator as Faker;
use Carbon\Carbon;

$factory->define(Taxa::class, function (Faker $faker) {
    return [
        //
        // 'servico_id'	=> 1,
        'nome'	=>	'Taxa de '.$faker->word.'',
        'valor'	=> $faker->randomFloat($nbMaxDecimals = 2, $min = 0, $max = 999),
        'observacoes' => $faker->sentence,
        'emissao'	=> Carbon::instance($faker->dateTimeThisMonth())->toDateTimeString(),
        'vencimento' => Carbon::instance($faker->dateTimeThisMonth())->toDateTimeString(),
        'situacao' => 'Pendente',

    ];
});
