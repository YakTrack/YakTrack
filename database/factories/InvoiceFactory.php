<?php

use App\Models\Client;
use App\Models\Invoice;
use Carbon\Carbon;
use Faker\Generator as Faker;

$factory->define(Invoice::class, function (Faker $faker) {
    return [
        'date'      => Carbon::today()->format('Y-m-d H:i:s'),
        'due_date'  => Carbon::today()->addDays(7)->format('Y-m-d H:i:s'),
        'number'    => function () use ($faker) {
            while (true) {
                $invoiceNumber = strtoupper($faker->word).'-'.$faker->randomNumber(3);
                if (!Invoice::where('number', $invoiceNumber)->exists()) {
                    return $invoiceNumber;
                }
            }
        },
        'client_id' => function () {
            return factory(Client::class)->create()->id;
        },
    ];
});
