<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Models\Unidade;
use Faker\Generator as Faker;

$factory->define(Unidade::class, function (Faker $faker) {
    return [
        //
        'codigo'    => Str::random(5),
        // 'empresa_id' => 2,
        'nomeFantasia' => $faker->name,
        'razaoSocial' => $faker->name,
        'cnpj' => '06643154000189',
        'inscricaoEst' => '213489321',
        'inscricaoMun' => '3291837129',
        'inscricaoImo'  => '9372198372198',
        'matriculaRI'   => Str::random(8),
        'tipoImovel'    => 'Shopping',
        
        'area'      => Str::random(4),
        'cidade'	=>	$faker->city,
        'uf'	=>	$faker->stateAbbr,
        'endereco' 	=>	$faker->streetAddress,
        'numero'    => Str::random(4),
        'complemento' => Str::random(6),
        'cep'	=>	$faker->postcode,
        'bairro'	=> 'Bairro nOvo',
        'telefone' => $faker->phone,
        'responsavel'	=>	$faker->name,
        'email' =>	$faker->email,
        // 'user_id' => 2,
    ];
});
