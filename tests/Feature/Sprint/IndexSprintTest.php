<?php

use App\Models\Project;
use App\Models\Sprint;

it('can view a list of sprints', function () {
    $project = Project::factory()->create();
    $sprints = Sprint::factory()->create(['project_id' => $project->id]);

    $this->actingAsUser();

    $response = $this->get(route('sprint.index'));

    $response->assertSuccessful();

    $sprints->each(function ($sprint) use ($response) {
        $response->assertSee($sprint->name);
        $response->assertSee($sprint->sessions->totalDurationForHumans());
    });
});
