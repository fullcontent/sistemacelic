<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Models\Unidade;
use Faker\Generator as Faker;

$factory->define(Unidade::class, function (Faker $faker) {
    return [
        //
        'codigo'    => Str::random(5),
        'empresa_id' => factory(App\Models\Empresa::class),
        'nomeFantasia' => $faker->name,
        'razaoSocial' => $faker->name,
        'cnpj' => Str::random(14),
        'inscricaoEst' => Str::random(5),
        'inscricaoMun' => Str::random(5),
        'cidade'	=>	$faker->city,
        'uf'	=>	'UF',
        'endereco' 	=>	$faker->address,
        'cep'	=>	'81925080',
        'bairro'	=> 'Bairro nOvo',
        'telefone' => '41-13123132131',
        'responsavel'	=>	$faker->name,
        'email' =>	$faker->email,
        'user_id' => factory(App\User::class),
    ];
});
