<?php

use App\Models\Client;
use App\Models\Project;
use App\Models\Session;
use App\Models\Task;

function sessionForClient(Client $client): Session
{
    return Session::factory()->create([
        'task_id' => Task::factory()->create([
            'project_id' => Project::factory()->create(['client_id' => $client->id])->id,
        ])->id,
    ]);
}

function sessionIdsInResponseDays($response): Illuminate\Support\Collection
{
    return collect($response->props()['days'])
        ->flatMap(fn ($day) => collect($day['sessions'])->pluck('id'));
}

it('only lists sessions for the focused client when focus is set', function () {
    $this->withoutExceptionHandling();

    $clientA = Client::factory()->create();
    $clientB = Client::factory()->create();

    $sessionA = sessionForClient($clientA);
    $sessionB = sessionForClient($clientB);

    $user = $this->actingAsUser();
    $user->update(['focused_client_id' => $clientA->id]);

    $response = $this->get(route('session.index', ['per-page' => 100]));

    $ids = sessionIdsInResponseDays($response);

    expect($ids)->toContain($sessionA->id)
        ->and($ids)->not->toContain($sessionB->id);
});

it('lists sessions for every client when no focus is set', function () {
    $this->withoutExceptionHandling();

    $clientA = Client::factory()->create();
    $clientB = Client::factory()->create();

    $sessionA = sessionForClient($clientA);
    $sessionB = sessionForClient($clientB);

    $this->actingAsUser();

    $response = $this->get(route('session.index', ['per-page' => 100]));

    $ids = sessionIdsInResponseDays($response);

    expect($ids)->toContain($sessionA->id)
        ->and($ids)->toContain($sessionB->id);
});
