<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Models\Target;
use Faker\Generator as Faker;
use Illuminate\Support\Carbon;

$factory->define(Target::class, function (Faker $faker) {
    return [
        'value_unit' => array_random(Target::VALUE_UNITS)['key'],
        'value' => $faker->numberBetween(0, 100),
    ];
});

$factory->state(Target::class, 'for_date', [
    'duration' => 1,
    'duration_unit' => Target::DURATION_UNITS['DAYS']['key'],
]);

$factory->state(Target::class, 'in_hours', [
    'value_unit' => Target::VALUE_UNITS['HOURS']['key'],
]);
