<?php

use App\Models\Project;
use App\Models\Sprint;
use Faker\Generator as Faker;

$factory->define(Sprint::class, function (Faker $faker) {
    return [
        'name'       => $faker->word(),
        'project_id' => function () {
            return factory(Project::class)->create();
        },
    ];
});

$factory->afterCreating(Sprint::class, function ($sprint) {
    $sprint->name = implode(' ', [
        $sprint->project->name,
        '-',
        'Sprint',
        ($sprint->id % $sprint->project->sprints()->count()) + 1,
    ]);
    $sprint->save();
});
