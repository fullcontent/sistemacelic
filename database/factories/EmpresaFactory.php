<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Models\Empresa;
use Faker\Generator as Faker;



$factory->define(Empresa::class, function (Faker $faker) {
    return [
        //
        
        'nomeFantasia' => $faker->name,
        'razaoSocial' => $faker->name,
        'cnpj' => '06643154000189',
        'inscricaoEst' => '12345690',
        'inscricaoMun' => '38989823',
        'cidade'	=>	$faker->city,
        'uf'	=>	$faker->stateAbbr,
        'endereco' 	=>	$faker->streetAddress,
        'cep'	=>	$faker->postcode,
        'bairro'	=> 'Bairro nOvo',
        'telefone' => $faker->phone,
        'responsavel'	=>	$faker->name,
        'email' =>	$faker->email,
        
        
    ];
});
