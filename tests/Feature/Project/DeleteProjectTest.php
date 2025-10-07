<?php

use App\Models\Project;
use App\Models\Sprint;
use App\Models\Task;

it('can delete a project', function () {
    $project = Project::factory()->create(['name' => 'Test Project']);

    $this->actingAsUser();

    $response = $this->delete(route('project.destroy', [
        'project' => $project,
    ]));

    $response->assertRedirect(route('project.index'));

    $this->assertDatabaseMissing('projects', [
        'id' => $project->id,
    ]);
});

it('cannot delete a project with sprints', function () {
    $project = Project::factory()->create(['name' => 'Test Project']);
    $sprint = Sprint::factory()->create(['project_id' => $project->id]);

    $this->actingAsUser();

    $response = $this->delete(route('project.destroy', [
        'project' => $project,
    ]));

    $response->assertStatus(422);
});

it('cannot delete a project with tasks', function () {
    $project = Project::factory()->create(['name' => 'Test Project']);
    $task = Task::factory()->create(['project_id' => $project->id]);

    $this->actingAsUser();

    $response = $this->delete(route('project.destroy', [
        'project' => $project,
    ]));

    $response->assertStatus(422);
});