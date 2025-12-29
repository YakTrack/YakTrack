<?php

use App\Models\Project;

it('can archive a project', function () {
    $project = Project::factory()->create();

    $this->actingAsUser();

    $response = $this->patch(route('project.archive', $project));

    $response->assertRedirect(route('project.index'));
    $response->assertSessionHas('success', "Project \"$project->name\" has been archived.");

    $project->refresh();
    expect($project->isArchived())->toBeTrue();
    expect($project->archived_at)->not->toBeNull();
});

it('can unarchive a project', function () {
    $project = Project::factory()->create(['archived_at' => now()]);

    $this->actingAsUser();

    $response = $this->patch(route('project.unarchive', $project));

    $response->assertRedirect(route('project.index'));
    $response->assertSessionHas('success', "Project \"$project->name\" has been unarchived.");

    $project->refresh();
    expect($project->isArchived())->toBeFalse();
    expect($project->archived_at)->toBeNull();
});

it('shows only non-archived projects on index', function () {
    $activeProject = Project::factory()->create();
    $archivedProject = Project::factory()->create(['archived_at' => now()]);

    $this->actingAsUser();

    $response = $this->get(route('project.index'));

    $response->assertSuccessful();
    $response->assertSee($activeProject->name);
    $response->assertDontSee($archivedProject->name);
});

it('shows archived projects on archived page', function () {
    $activeProject = Project::factory()->create();
    $archivedProject = Project::factory()->create(['archived_at' => now()]);

    $this->actingAsUser();

    $response = $this->get(route('project.archived'));

    $response->assertSuccessful();
    $response->assertSee($archivedProject->name);
    $response->assertDontSee($activeProject->name);
});

it('can view archived project details', function () {
    $archivedProject = Project::factory()->create(['archived_at' => now()]);

    $this->actingAsUser();

    $response = $this->get(route('project.show', $archivedProject));

    $response->assertSuccessful();
    $response->assertSee($archivedProject->name);
});

it('project model has correct archive methods', function () {
    $project = Project::factory()->create();

    expect($project->isArchived())->toBeFalse();

    $project->archive();
    $project->refresh();

    expect($project->isArchived())->toBeTrue();
    expect($project->archived_at)->not->toBeNull();

    $project->unarchive();
    $project->refresh();

    expect($project->isArchived())->toBeFalse();
    expect($project->archived_at)->toBeNull();
});

it('project scopes work correctly', function () {
    $activeProject = Project::factory()->create();
    $archivedProject = Project::factory()->create(['archived_at' => now()]);

    $archivedProjects = Project::archived()->get();
    $activeProjects = Project::notArchived()->get();

    expect($archivedProjects)->toHaveCount(1);
    expect($archivedProjects->first()->id)->toBe($archivedProject->id);

    expect($activeProjects)->toHaveCount(1);
    expect($activeProjects->first()->id)->toBe($activeProject->id);
});

it('excludes archived projects from client portal projects index', function () {
    $client = \App\Models\Client::factory()->create();
    $clientUser = \App\Models\ClientUser::factory()->create([
        'client_id' => $client->id,
    ]);
    $activeProject = Project::factory()->create([
        'client_id' => $client->id,
    ]);
    $archivedProject = Project::factory()->create([
        'client_id'   => $client->id,
        'archived_at' => now(),
    ]);

    $response = $this->actingAs($clientUser, 'client')
        ->get('/client-portal/projects');

    $response->assertSuccessful();
    $response->assertInertia(
        fn ($page) => $page
        ->component('ClientPortal/Projects/Index')
        ->has('projects', 1)
        ->where('projects.0.id', $activeProject->id)
    );
});

it('excludes archived projects from client portal dashboard', function () {
    $client = \App\Models\Client::factory()->create();
    $clientUser = \App\Models\ClientUser::factory()->create([
        'client_id' => $client->id,
    ]);
    $activeProject = Project::factory()->create([
        'client_id' => $client->id,
    ]);
    $archivedProject = Project::factory()->create([
        'client_id'   => $client->id,
        'archived_at' => now(),
    ]);

    $response = $this->actingAs($clientUser, 'client')
        ->get('/client-portal');

    $response->assertSuccessful();
    $response->assertInertia(
        fn ($page) => $page
        ->component('ClientPortal/Dashboard')
        ->has('projects', 1)
        ->where('projects.0.id', $activeProject->id)
    );
});

it('prevents accessing archived project via client portal show', function () {
    $client = \App\Models\Client::factory()->create();
    $clientUser = \App\Models\ClientUser::factory()->create([
        'client_id' => $client->id,
    ]);
    $archivedProject = Project::factory()->create([
        'client_id'   => $client->id,
        'archived_at' => now(),
    ]);

    $response = $this->actingAs($clientUser, 'client')
        ->get('/client-portal/projects/'.$archivedProject->id);

    $response->assertNotFound();
});

it('prevents accessing archived project via client portal report download', function () {
    $client = \App\Models\Client::factory()->create();
    $clientUser = \App\Models\ClientUser::factory()->create([
        'client_id' => $client->id,
    ]);
    $archivedProject = Project::factory()->create([
        'client_id'   => $client->id,
        'archived_at' => now(),
    ]);

    $response = $this->actingAs($clientUser, 'client')
        ->get('/client-portal/projects/'.$archivedProject->id.'/report');

    $response->assertNotFound();
});

it('prevents accessing archived project via client portal report view', function () {
    $client = \App\Models\Client::factory()->create();
    $clientUser = \App\Models\ClientUser::factory()->create([
        'client_id' => $client->id,
    ]);
    $archivedProject = Project::factory()->create([
        'client_id'   => $client->id,
        'archived_at' => now(),
    ]);

    $response = $this->actingAs($clientUser, 'client')
        ->get('/client-portal/projects/'.$archivedProject->id.'/report/view');

    $response->assertNotFound();
});
