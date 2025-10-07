<?php

use App\Models\Client;

it('can visit the create project page', function () {
    $client = Client::factory()->create([
        'name' => 'O\'Reilly Apostropheson',
    ]);

    $this->withoutExceptionHandling();

    $this->actingAsUser();

    $response = $this->get(route('project.create'));

    $response->assertSuccessful();

    $response->assertSee($client->name);
});

it('can submit a post request to create a project', function () {
    $client = Client::factory()->create();

    $this->withoutExceptionHandling();

    $this->actingAsUser();

    $response = $this->post(route('project.store'), [
        'name'        => 'Test Project',
        'description' => 'Test project description.',
        'client_id'   => $client->id,
    ]);

    $response->assertRedirect(route('project.index'));

    $this->assertDatabaseHas('projects', [
        'name'        => 'Test Project',
        'description' => 'Test project description.',
        'client_id'   => $client->id,
    ]);
});