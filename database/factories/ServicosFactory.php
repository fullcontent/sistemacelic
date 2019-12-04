<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Models\Servico;
use Faker\Generator as Faker;
use Carbon\Carbon;

$factory->define(Servico::class, function (Faker $faker) {
    return [
        //
         //
        'tipo'    => 'Primario',
        'nome' => 'Alvara de '.$faker->word.'',
        'os'    => Str::random(5),
        'protocolo_emissao'	=> Carbon::instance($faker->dateTimeThisMonth())->toDateTimeString(),
        'protocolo_validade'	=> 	Carbon::instance($faker->dateTimeThisMonth())->toDateTimeString(),
        'protocolo_anexo' => Str::random(10),
        'situacao'	=> 'situacao',
        'observacoes'	=> 'Observacoes',
        
        
        'pendencia'	=>	$faker->sentence,
        'acao'	=>	'acao',

        // 'empresa_id' => 4,
        // 'unidade_id' => null
    ];
});
