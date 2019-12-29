<?php

use App\Models\Project;
use App\Models\Task;
use Faker\Generator as Faker;

$factory->define(Task::class, function (Faker $faker) {
    return [
        'name'        => $faker->sentence,
        'description' => $faker->sentence,
        'status'      => $faker->word,
        'project_id' => function () {
            return factory(Project::class)->create()->id;
        },
    ];
});
