<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Models\Pendencia;
use Faker\Generator as Faker;
use Carbon\Carbon;

$factory->define(Pendencia::class, function (Faker $faker) {
    return [
        //
         //
        // 'servico_id' => 1,
        'created_by'	=>	1,
        'pendencia' => $faker->sentence,
        'vencimento' => Carbon::instance($faker->dateTimeThisMonth())->toDateTimeString(),
        'responsavel_id' => 1,
        'responsavel_tipo' => 'user',
        'status' => 'pendente',
    ];
});
