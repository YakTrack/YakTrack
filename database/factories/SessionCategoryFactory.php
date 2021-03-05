<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Models\SessionCategory;
use Faker\Generator as Faker;

$factory->define(SessionCategory::class, function (Faker $faker) {
    return [
        'name' => $faker->word,
    ];
});
