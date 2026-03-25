<?php

use App\Models\Project;
use App\Models\Sprint;

it('can view a list of sprints', function () {
    $project = Project::factory()->create();
    $sprint = Sprint::factory()->afterCreating(fn (Sprint $s) => $s->projects()->sync([$project->id]))->create();

    $this->actingAsUser();

    $response = $this->get(route('sprint.index'));

    $response->assertSuccessful();

    $response->assertSee($sprint->name);
    $response->assertSee($sprint->sessions->totalDurationForHumans());
});
