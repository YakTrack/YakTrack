<?php

use App\Models\AcceptanceCriteria;
use App\Models\Client;
use App\Models\Feature;
use App\Models\Invoice;
use App\Models\Project;
use App\Models\Session;
use App\Models\Sprint;
use App\Models\Task;
use App\Models\TestRun;

it('no-ops the project scope when no client is focused', function () {
    Project::factory()->create();
    Project::factory()->create();

    expect(Project::forFocusedClient(null)->count())->toBe(2);
});

it('filters projects to the focused client', function () {
    $clientA = Client::factory()->create();
    $clientB = Client::factory()->create();

    $projectA = Project::factory()->create(['client_id' => $clientA->id]);
    Project::factory()->create(['client_id' => $clientB->id]);

    $results = Project::forFocusedClient($clientA->id)->get();

    expect($results)->toHaveCount(1)
        ->and($results->first()->id)->toBe($projectA->id);
});

it('filters invoices to the focused client', function () {
    $clientA = Client::factory()->create();
    $clientB = Client::factory()->create();

    $invoiceA = Invoice::factory()->create(['client_id' => $clientA->id]);
    Invoice::factory()->create(['client_id' => $clientB->id]);

    expect(Invoice::forFocusedClient(null)->count())->toBe(2);

    $results = Invoice::forFocusedClient($clientA->id)->get();

    expect($results)->toHaveCount(1)
        ->and($results->first()->id)->toBe($invoiceA->id);
});

it('filters tasks to the focused client via their project', function () {
    $clientA = Client::factory()->create();
    $clientB = Client::factory()->create();

    $taskA = Task::factory()->create([
        'project_id' => Project::factory()->create(['client_id' => $clientA->id])->id,
    ]);
    Task::factory()->create([
        'project_id' => Project::factory()->create(['client_id' => $clientB->id])->id,
    ]);

    expect(Task::forFocusedClient(null)->count())->toBe(2);

    $results = Task::forFocusedClient($clientA->id)->get();

    expect($results)->toHaveCount(1)
        ->and($results->first()->id)->toBe($taskA->id);
});

it('filters features to the focused client via their project', function () {
    $clientA = Client::factory()->create();
    $clientB = Client::factory()->create();

    $featureA = Feature::factory()->create([
        'project_id' => Project::factory()->create(['client_id' => $clientA->id])->id,
    ]);
    Feature::factory()->create([
        'project_id' => Project::factory()->create(['client_id' => $clientB->id])->id,
    ]);

    expect(Feature::forFocusedClient(null)->count())->toBe(2);

    $results = Feature::forFocusedClient($clientA->id)->get();

    expect($results)->toHaveCount(1)
        ->and($results->first()->id)->toBe($featureA->id);
});

it('filters acceptance criteria to the focused client via their project', function () {
    $clientA = Client::factory()->create();
    $clientB = Client::factory()->create();

    $criteriaA = AcceptanceCriteria::factory()->create([
        'project_id' => Project::factory()->create(['client_id' => $clientA->id])->id,
    ]);
    AcceptanceCriteria::factory()->create([
        'project_id' => Project::factory()->create(['client_id' => $clientB->id])->id,
    ]);

    expect(AcceptanceCriteria::forFocusedClient(null)->count())->toBe(2);

    $results = AcceptanceCriteria::forFocusedClient($clientA->id)->get();

    expect($results)->toHaveCount(1)
        ->and($results->first()->id)->toBe($criteriaA->id);
});

it('filters test runs to the focused client via their project', function () {
    $clientA = Client::factory()->create();
    $clientB = Client::factory()->create();

    $testRunA = TestRun::factory()->create([
        'project_id' => Project::factory()->create(['client_id' => $clientA->id])->id,
    ]);
    TestRun::factory()->create([
        'project_id' => Project::factory()->create(['client_id' => $clientB->id])->id,
    ]);

    expect(TestRun::forFocusedClient(null)->count())->toBe(2);

    $results = TestRun::forFocusedClient($clientA->id)->get();

    expect($results)->toHaveCount(1)
        ->and($results->first()->id)->toBe($testRunA->id);
});

it('filters sessions to the focused client via task and project', function () {
    $clientA = Client::factory()->create();
    $clientB = Client::factory()->create();

    $sessionA = Session::factory()->create([
        'task_id' => Task::factory()->create([
            'project_id' => Project::factory()->create(['client_id' => $clientA->id])->id,
        ])->id,
    ]);
    Session::factory()->create([
        'task_id' => Task::factory()->create([
            'project_id' => Project::factory()->create(['client_id' => $clientB->id])->id,
        ])->id,
    ]);

    expect(Session::forFocusedClient(null)->count())->toBe(2);

    $results = Session::forFocusedClient($clientA->id)->get();

    expect($results)->toHaveCount(1)
        ->and($results->first()->id)->toBe($sessionA->id);
});

it('filters sprints to those touching the focused client projects', function () {
    $clientA = Client::factory()->create();
    $clientB = Client::factory()->create();

    $sprintA = Sprint::factory()->create();
    $sprintA->projects()->sync([Project::factory()->create(['client_id' => $clientA->id])->id]);

    $sprintB = Sprint::factory()->create();
    $sprintB->projects()->sync([Project::factory()->create(['client_id' => $clientB->id])->id]);

    expect(Sprint::forFocusedClient(null)->count())->toBe(2);

    $results = Sprint::forFocusedClient($clientA->id)->get();

    expect($results)->toHaveCount(1)
        ->and($results->first()->id)->toBe($sprintA->id);
});
