<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Aluno;
use Faker\Generator as Faker;

$factory->define(App\Aluno::class, function (Faker $faker) {
    return [
        "nome" => $faker->unique()->name,
		"email" => $faker->unique()->safeEmail,
		"datadenascimento" => $faker->date($format = 'Y-m-d', $max = 'now'),
    ];
});
