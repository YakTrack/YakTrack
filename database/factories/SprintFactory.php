<?php

use App\Models\Project;
use App\Models\Sprint;
use Faker\Generator as Faker;

// Sprint factory
$factory->define(Sprint::class, function (Faker $faker) {
    return [
        'name'       => $faker->word(),
        'project_id' => function () {
            return factory(Project::class)->create();
        },
    ];
});
