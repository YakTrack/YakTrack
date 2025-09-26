<?php

/*
|--------------------------------------------------------------------------
| Model Factories
|--------------------------------------------------------------------------
|
| This file can be used to define legacy factory definitions if needed.
| All factories have been converted to the new Laravel 8+ syntax using
| the Factory classes in the Database\Factories namespace.
|
*/

// User factory (legacy syntax for backward compatibility)
$factory->define(App\Models\User::class, function (Faker\Generator $faker) {
    return [
        'name'           => $faker->name,
        'email'          => $faker->safeEmail,
        'password'       => bcrypt(str_random(10)),
        'remember_token' => str_random(10),
    ];
});