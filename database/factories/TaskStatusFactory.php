<?php

use App\Models\Project;
use App\Models\TaskStatus;
use Faker\Generator as Faker;

$factory->define(TaskStatus::class, function (Faker $faker) {
    return [
        'name' => $faker->words(2, true),
        'color' => $faker->hexColor(),
        'sort_order' => $faker->numberBetween(0, 10),
        'is_default' => false,
        'is_completed' => false,
        'project_id' => function () {
            return factory(Project::class)->create()->id;
        },
    ];
});
