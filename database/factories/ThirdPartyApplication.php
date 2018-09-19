<?php

use Faker\Generator as Faker;

$factory->define(App\Models\ThirdPartyApplication::class, function (Faker $faker) {
    return [
        'name' => $faker->word,
    ];
});

$factory->state(App\Models\ThirdPartyApplication::class, 'wrike', function (Faker $faker) {
    return [
        'type' => 'wrike',
    ];
});
