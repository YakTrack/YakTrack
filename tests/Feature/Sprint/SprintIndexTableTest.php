<?php

use App\Models\Project;
use App\Models\Session;
use App\Models\Sprint;

it('paginates sprints on the index', function () {
    Sprint::factory()->count(20)->create();

    $this->actingAsUser();

    $response = $this->get(route('sprint.index', ['per_page' => 10]));

    $response->assertSuccessful();
    $response->assertInertia(fn ($page) => $page
        ->component('Sprint/Index')
        ->has('sprints.data', 10)
        ->where('sprints.per_page', 10)
        ->where('sprints.total', 20));
});

it('filters sprints by search on sprint name', function () {
    $match = Sprint::factory()->create();
    $match->update(['name' => 'UniqueSprintNameXyz']);
    $other = Sprint::factory()->create();
    $other->update(['name' => 'OtherSprint']);

    $this->actingAsUser();

    $response = $this->get(route('sprint.index', ['q' => 'UniqueSprintName']));

    $response->assertSuccessful();
    $response->assertInertia(fn ($page) => $page
        ->has('sprints.data', 1)
        ->where('sprints.data.0.id', $match->id));
});

it('filters sprints by linked project name', function () {
    $project = Project::factory()->create(['name' => 'UniqueProjectForSprintSearch']);
    $match = Sprint::factory()->afterCreating(fn (Sprint $s) => $s->projects()->sync([$project->id]))->create();
    Sprint::factory()->create();

    $this->actingAsUser();

    $response = $this->get(route('sprint.index', ['q' => 'UniqueProjectForSprint']));

    $response->assertSuccessful();
    $response->assertInertia(fn ($page) => $page
        ->has('sprints.data', 1)
        ->where('sprints.data.0.id', $match->id));
});

it('filters sprints by project', function () {
    $projectA = Project::factory()->create();
    $projectB = Project::factory()->create();
    $sprintA = Sprint::factory()->afterCreating(fn (Sprint $s) => $s->projects()->sync([$projectA->id]))->create();
    Sprint::factory()->afterCreating(fn (Sprint $s) => $s->projects()->sync([$projectB->id]))->create();

    $this->actingAsUser();

    $response = $this->get(route('sprint.index', ['project_id' => $projectA->id]));

    $response->assertSuccessful();
    $response->assertInertia(fn ($page) => $page
        ->has('sprints.data', 1)
        ->where('sprints.data.0.id', $sprintA->id));
});

it('filters sprints by lifecycle', function () {
    $open = Sprint::factory()->create(['is_open' => true]);
    Sprint::factory()->create(['is_open' => false]);

    $this->actingAsUser();

    $response = $this->get(route('sprint.index', ['lifecycle' => 'open']));

    $response->assertSuccessful();
    $response->assertInertia(fn ($page) => $page
        ->has('sprints.data', 1)
        ->where('sprints.data.0.id', $open->id));
});

it('sorts sprints by total session duration', function () {
    $project = Project::factory()->create();
    $shorter = Sprint::factory()->afterCreating(fn (Sprint $s) => $s->projects()->sync([$project->id]))->create();
    $longer = Sprint::factory()->afterCreating(fn (Sprint $s) => $s->projects()->sync([$project->id]))->create();

    Session::factory()->create([
        'sprint_id'  => $shorter->id,
        'started_at' => now()->subHour(),
        'ended_at'   => now()->subHour()->addSeconds(60),
    ]);
    Session::factory()->create([
        'sprint_id'  => $longer->id,
        'started_at' => now()->subHours(2),
        'ended_at'   => now()->subHours(2)->addSeconds(3600),
    ]);

    $this->actingAsUser();

    $response = $this->get(route('sprint.index', [
        'sort'      => 'duration',
        'direction' => 'asc',
    ]));

    $response->assertSuccessful();
    $response->assertInertia(fn ($page) => $page
        ->where('sprints.data.0.id', $shorter->id)
        ->where('sprints.data.1.id', $longer->id));
});

it('rejects invalid per_page', function () {
    $this->actingAsUser();

    $this->from(route('sprint.index'))
        ->get(route('sprint.index', ['per_page' => 99]))
        ->assertInvalid(['per_page']);
});

it('includes table state for the frontend', function () {
    $project = Project::factory()->create();
    $sprint = Sprint::factory()->afterCreating(fn (Sprint $s) => $s->projects()->sync([$project->id]))->create();
    $sprint->update(['name' => 'Listed Sprint']);

    $this->actingAsUser();

    $response = $this->get(route('sprint.index', [
        'q'           => 'Listed',
        'project_id'  => $project->id,
        'lifecycle'   => 'open',
        'sort'        => 'name',
        'direction'   => 'asc',
        'per_page'    => 25,
    ]));

    $response->assertSuccessful();
    $response->assertInertia(fn ($page) => $page
        ->component('Sprint/Index')
        ->where('table.filters.q', 'Listed')
        ->where('table.filters.project_id', (string) $project->id)
        ->where('table.filters.lifecycle', 'open')
        ->where('table.sort', 'name')
        ->where('table.direction', 'asc')
        ->where('table.per_page', 25));
});
