<?php

use App\Models\Client;
use App\Models\Project;
use App\Support\FactoryGenerator;
use Faker\Generator as Faker;


$factory->define(Project::class, function (Faker $faker) {
    return [
        'name'          => ucfirst(app(FactoryGenerator::class)->projectName()),
        'client_id'     => factory(Client::class)->create(),
        'description'   => $faker->sentence,
    ];
});
