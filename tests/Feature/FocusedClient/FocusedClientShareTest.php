<?php

use App\Models\Client;
use App\Models\ClientUser;
use App\Models\Project;
use App\Models\Session;
use App\Models\Task;

it('does not share focus data with client-portal users', function () {
    $client = Client::factory()->create();
    $clientUser = ClientUser::factory()->create([
        'client_id' => $client->id,
    ]);

    $response = $this->actingAs($clientUser, 'client')
        ->get('/client-portal/projects');

    $response->assertStatus(200);
    $response->assertInertia(fn ($page) => $page
        ->missing('focusableClients')
        ->missing('focusedClient'));
});

it('shares every client to web users regardless of weekly activity', function () {
    $this->withoutExceptionHandling();

    // A client with a session this week (would appear in Home's page-level rollup)...
    $activeClient = Client::factory()->create();
    Session::factory()->create([
        'started_at' => now(),
        'ended_at'   => now()->addHour(),
        'task_id'    => Task::factory()->create([
            'project_id' => Project::factory()->create(['client_id' => $activeClient->id])->id,
        ])->id,
    ]);

    // ...and a client with no sessions this week (absent from that rollup).
    $quietClient = Client::factory()->create();

    $this->actingAsUser();

    $response = $this->get(route('home'));

    $response->assertSuccessful();
    $response->assertHasProp('focusableClients');
    $response->assertPropCount('focusableClients', 2);

    $ids = collect($response->props('focusableClients'))->pluck('id');
    expect($ids)->toContain($activeClient->id)
        ->and($ids)->toContain($quietClient->id);
});
