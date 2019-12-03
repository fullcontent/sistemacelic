<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Model\Servico;
use Faker\Generator as Faker;

$factory->define(Servico::class, function (Faker $faker) {
    return [
        //
         //
        'tipo'    => Str::random(5),
        'nome' => $faker->name,
        'emissao'	=> '21/10/2019',
        'validade'	=> 	'12/12/2020',
        'protocolo' => Str::random(4),
        'situacao'	=> 'situacao',
        'observacoes'	=> 'Observacoes',
        'meta'	=> '12/12/2010',
        'historico' => 1,
        'pendencia'	=>	'pendencia',
        'acao'	=>	'acao',

        'empresa_id' => 4,
        'unidade_id' => null
    ];
});
