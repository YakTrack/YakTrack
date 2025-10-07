<?php

use App\Models\Client;
use App\Models\Project;

it('can view the page to edit a project', function () {
    $clients = Client::factory()->count(2)->create();
    $project = Project::factory()->create(['client_id' => $clients[0]->id]);

    $this->actingAsUser();

    $response = $this->get(route('project.edit', ['project' => $project]));

    $response->assertSuccessful();

    $response->assertSee($project->name);
    $clients->each(function ($client) use ($response) {
        $response->assertSee(e($client->name));
    });
});

it('can update a project with a patch request', function () {
    $clients = Client::factory()->count(2)->create();
    $project = Project::factory()->create(['client_id' => $clients[0]->id]);

    $this->actingAsUser();

    $response = $this->patch(
        route('project.update', ['project' => $project]),
        $updatedProjectDetails = [
            'name'        => 'Updated Project',
            'description' => 'Updated project description',
            'client_id'   => $clients[1]->id,
        ]
    );

    $response->assertRedirect(route('project.index'));

    $this->assertDatabaseHas('projects', $updatedProjectDetails);
});