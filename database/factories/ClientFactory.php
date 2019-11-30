<?php

use App\Models\Client;
use Faker\Generator as Faker;

// Client factory
$factory->define(Client::class, function (Faker $faker) {
    return [
        'name'  => $faker->name,
        'email' => $faker->safeEmail,
    ];
});
