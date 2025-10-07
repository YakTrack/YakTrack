<?php

use App\Models\Project;

it('can visit create task status page', function () {
    $this->actingAsUser();

    $project = Project::factory()->create();

    $response = $this->get(route('task-status.create', ['project_id' => $project->id]));

    $response->assertStatus(200);
    $response->assertHasProp('projects');
    $response->assertHasProp('project');

    $projectData = $response->props('project');
    expect($projectData['id'])->toBe($project->id);
});

it('can visit create task status page without project id', function () {
    $this->actingAsUser();

    $response = $this->get(route('task-status.create'));

    $response->assertStatus(200);
    $response->assertHasProp('projects');
    $response->assertPropValue('project', null);
});
