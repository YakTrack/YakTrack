<?php

use Faker\Generator as Faker;

$factory->define(App\Models\Invoice::class, function (Faker $faker) {
    return [
        'number' => strtoupper($faker->word).'-'.$faker->randomNumber(3),
    ];
});
