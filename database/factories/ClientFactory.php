<?php

use App\Models\Client;
use App\Models\Invoice;
use Faker\Generator as Faker;

$factory->define(Client::class, function (Faker $faker) {
    return [
        'name'  => $faker->name,
        'email' => $faker->safeEmail,
    ];
});

$factory->afterCreatingState(Client::class, 'with_invoices', function ($client) {
    factory(Invoice::class, 10)->create(['client_id' => $client->id]);
});
