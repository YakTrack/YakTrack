<?php

use App\Models\Client;

it('pre-selects the focused client on the create project page', function () {
    $this->withoutExceptionHandling();

    $client = Client::factory()->create();
    $user = $this->actingAsUser();
    $user->update(['focused_client_id' => $client->id]);

    $response = $this->get(route('project.create'));

    $response->assertSuccessful();
    $response->assertPropValue('focusedClientId', $client->id);
});

it('does not pre-select a client when no client is focused', function () {
    $this->withoutExceptionHandling();

    $this->actingAsUser();

    $response = $this->get(route('project.create'));

    $response->assertSuccessful();
    $response->assertPropValue('focusedClientId', null);
});
