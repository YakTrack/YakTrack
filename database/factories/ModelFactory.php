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

// User factory
$factory->define(App\Models\User::class, function (Faker\Generator $faker) {
    return [
        'name'           => $faker->name,
        'email'          => $faker->safeEmail,
        'password'       => bcrypt(str_random(10)),
        'remember_token' => str_random(10),
    ];
});

// Project factory
$factory->define(App\Models\Project::class, function (Faker\Generator $faker) {
    return [
        'name'        => $faker->word(),
        'description' => $faker->sentence,
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
