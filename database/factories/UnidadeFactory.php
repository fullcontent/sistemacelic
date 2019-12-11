<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Models\Unidade;
use Faker\Generator as Faker;

$factory->define(Unidade::class, function (Faker $faker) {
    return [
        //
        'nomeFantasia' => 'Empresa '.$faker->name,
        'razaoSocial' => $faker->name,
        'cnpj' => '06643154000189',
        'inscricaoEst' => '12345690',
        'inscricaoMun' => '38989823',
        'inscricaoImo'  => '739821739173',
        'status'        => 'Ativa',
        
        'matriculaRI'   => Str::random(8),
        'tipoImovel'    => 'Shopping',
        'codigo'    =>  Str::random(3),
        'area'      => Str::random(4),

        'cidade'    =>  $faker->city,
        'uf'    =>  $faker->stateAbbr,
        'endereco'  =>  $faker->streetAddress,
        'numero'    => Str::random(4),
        'complemento' => Str::random(6),
        'cep'   =>  $faker->postcode,
        'bairro'    => 'Bairro nOvo',
        'telefone' => $faker->phone,
        'responsavel'   =>  $faker->name,
        'email' =>  $faker->email,
    ];
});
