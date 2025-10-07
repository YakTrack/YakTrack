<?php

use App\Models\Project;

it('can see a list of projects', function () {
    $this->withoutExceptionHandling();

    $project = Project::factory()->create();

    $this->actingAsUser();

    $response = $this->get(route('project.index'));

    $response->assertSuccessful();

    $response->assertSee($project->name);
});