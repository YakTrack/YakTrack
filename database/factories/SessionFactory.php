<?php

use App\Models\Session;
use Carbon\Carbon;
use Faker\Generator as Faker;

// Session factory
$factory->define(Session::class, function (Faker $faker) {
    return [
        'started_at' => $startedAt = Carbon::instance($faker->dateTimeThisYear()),
        'ended_at'   => (clone $startedAt)->addSeconds($faker->randomNumber(4)),
    ];
});
