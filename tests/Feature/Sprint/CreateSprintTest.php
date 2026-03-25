<?php

use App\Models\Project;
use App\Models\Sprint;

it('can view the form to create a sprint', function () {
    $project = Project::factory()->create();

    $this->actingAsUser();

    $response = $this->get(route('sprint.create'));

    $response->assertSuccessful();

    $response->assertSee($project->name);
});

it('can store a new sprint with a post request', function () {
    $project = Project::factory()->create();

    $this->actingAsUser();

    $response = $this->post(route('sprint.store'), [
        'name'        => 'New sprint',
        'project_ids' => [$project->id],
        'is_open'    => array_random([0, 1]),
    ]);

    $response->assertRedirect(route('sprint.index'));

    $this->assertDatabaseHas('sprints', ['name' => 'New sprint']);
    $sprint = Sprint::where('name', 'New sprint')->first();
    expect($sprint->projects()->pluck('id')->toArray())->toContain($project->id);
});
