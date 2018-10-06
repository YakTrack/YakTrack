<?php

/*
|--------------------------------------------------------------------------
| Model Factories
|--------------------------------------------------------------------------
|
| Here you may define all of your model factories. Model factories give
| you a convenient way to create models for testing and seeding your
| database. Just tell the factory how a default model should look.
|
*/

use Carbon\Carbon;

// User factory
$factory->define(App\Models\User::class, function (Faker\Generator $faker) {
    return [
        'name'           => $faker->name,
        'email'          => $faker->safeEmail,
        'password'       => bcrypt(str_random(10)),
        'remember_token' => str_random(10),
    ];
});

// Client factory
$factory->define(App\Models\Client::class, function (Faker\Generator $faker) {
    return [
        'name'  => $faker->name,
        'email' => $faker->safeEmail,
    ];
});

// Project factory
$factory->define(App\Models\Project::class, function (Faker\Generator $faker) {
    return [
        'name'        => $faker->word(),
        'description' => $faker->sentence,
    ];
});

// Sprint factory
$factory->define(App\Models\Sprint::class, function (Faker\Generator $faker) {
    return [
        'name' => $faker->word(),
    ];
});

// Task factory
$factory->define(App\Models\Task::class, function (Faker\Generator $faker) {
    return [
        'name'        => $faker->sentence,
        'description' => $faker->sentence,
        'status'      => $faker->word,
    ];
});

// Session factory
$factory->define(App\Models\Session::class, function (Faker\Generator $faker) {
    return [
        'started_at' => Carbon::now()->format('Y-m-d H:i:s'),
        'ended_at'   => Carbon::now()->format('Y-m-d H:i:s'),
    ];
});
