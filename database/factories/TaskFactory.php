<?php

use App\Models\Project;
use App\Models\Task;
use App\Support\FactoryGenerator;
use Faker\Generator as Faker;

$factory->define(Task::class, function (Faker $faker) {
    return [
        'name'          => ucfirst(app(FactoryGenerator::class)->taskName()),
        'description' => $faker->paragraph,
        'status'      => $faker->word,
        'project_id' => function () {
            return factory(Project::class)->create()->id;
        },
    ];
});
