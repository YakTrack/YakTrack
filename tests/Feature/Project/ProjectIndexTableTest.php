<?php

use App\Models\Client;
use App\Models\Project;

it('paginates projects on the index', function () {
    Project::factory()->count(20)->create();

    $this->actingAsUser();

    $response = $this->get(route('project.index', ['per_page' => 10]));

    $response->assertSuccessful();
    $response->assertInertia(fn ($page) => $page
        ->component('Project/Index')
        ->has('projects.data', 10)
        ->where('projects.per_page', 10)
        ->where('projects.total', 20));
});

it('filters projects by search query', function () {
    $match = Project::factory()->create(['name' => 'UniqueAlphaName']);
    Project::factory()->create(['name' => 'OtherBetaName']);

    $this->actingAsUser();

    $response = $this->get(route('project.index', ['q' => 'UniqueAlpha']));

    $response->assertSuccessful();
    $response->assertInertia(fn ($page) => $page
        ->has('projects.data', 1)
        ->where('projects.data.0.id', $match->id));
});

it('filters projects by client', function () {
    $clientA = Client::factory()->create(['name' => 'Client A']);
    $clientB = Client::factory()->create(['name' => 'Client B']);
    $projectA = Project::factory()->create(['client_id' => $clientA->id]);
    Project::factory()->create(['client_id' => $clientB->id]);

    $this->actingAsUser();

    $response = $this->get(route('project.index', ['client_id' => $clientA->id]));

    $response->assertSuccessful();
    $response->assertInertia(fn ($page) => $page
        ->has('projects.data', 1)
        ->where('projects.data.0.id', $projectA->id));
});

it('sorts projects by client name', function () {
    $clientZ = Client::factory()->create(['name' => 'Zebra Co']);
    $clientA = Client::factory()->create(['name' => 'Alpha Co']);
    Project::factory()->create(['name' => 'P1', 'client_id' => $clientZ->id]);
    Project::factory()->create(['name' => 'P2', 'client_id' => $clientA->id]);

    $this->actingAsUser();

    $response = $this->get(route('project.index', [
        'sort'      => 'client',
        'direction' => 'asc',
    ]));

    $response->assertSuccessful();
    $response->assertInertia(fn ($page) => $page
        ->where('projects.data.0.name', 'P2')
        ->where('projects.data.1.name', 'P1'));
});

it('rejects invalid per_page', function () {
    $this->actingAsUser();

    $this->from(route('project.index'))
        ->get(route('project.index', ['per_page' => 99]))
        ->assertInvalid(['per_page']);
});

it('includes table state for the frontend', function () {
    Project::factory()->create(['name' => 'Listed']);

    $this->actingAsUser();

    $response = $this->get(route('project.index', [
        'q'           => 'Listed',
        'sort'        => 'name',
        'direction'   => 'desc',
        'per_page'    => 25,
    ]));

    $response->assertSuccessful();
    $response->assertInertia(fn ($page) => $page
        ->component('Project/Index')
        ->where('table.filters.q', 'Listed')
        ->where('table.sort', 'name')
        ->where('table.direction', 'desc')
        ->where('table.per_page', 25));
});
