<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Curso;
use Faker\Generator as Faker;

$factory->define(App\Curso::class, function (Faker $faker) {
	$title = $faker->unique()->word(8);
    return [
        "titulo" => $title,
		"descricao" => $faker->text(400),
    ];
});
