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
        'protocolo_numero' => Str::random(10),
        'protocolo_anexo' => Str::random(10),
        'responsavel_id' => 1,
        'situacao'	=> 'Em Andamento',
        'observacoes'	=> 'Observacoes',
        
        
        

        // 'empresa_id' => 4,
        // 'unidade_id' => null
    ];
});
