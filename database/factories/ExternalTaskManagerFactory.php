<?php

use Faker\Generator as Faker;

$factory->define(App\Models\ExternalTaskManager::class, function (Faker $faker) {
    return [
        'name' => $faker->word,
    ];
});

$factory->state(App\Models\ExternalTaskManager::class, 'wrike', function (Faker $faker) {
    return [
        'type' => 'wrike',
    ];
});
