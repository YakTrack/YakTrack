<?php

use App\Models\Client;

it('persists the focused client for the user', function () {
    $this->withoutExceptionHandling();

    $user = $this->actingAsUser();
    $client = Client::factory()->create();

    $response = $this->patch(route('focused-client.update'), [
        'client_id' => $client->id,
    ]);

    $response->assertRedirect();

    $this->assertDatabaseHas('users', [
        'id'                => $user->id,
        'focused_client_id' => $client->id,
    ]);
});

it('clears the focused client when no client is given', function () {
    $client = Client::factory()->create();
    $user = $this->actingAsUser();
    $user->update(['focused_client_id' => $client->id]);

    $this->withoutExceptionHandling();

    $response = $this->patch(route('focused-client.update'), [
        'client_id' => null,
    ]);

    $response->assertRedirect();

    $this->assertDatabaseHas('users', [
        'id'                => $user->id,
        'focused_client_id' => null,
    ]);
});

it('nulls the focused client when that client is deleted', function () {
    $client = Client::factory()->create();
    $user = $this->actingAsUser();
    $user->update(['focused_client_id' => $client->id]);

    $client->delete();

    expect($user->fresh()->focused_client_id)->toBeNull();
});

it('rejects a non-existent client', function () {
    $user = $this->actingAsUser();

    $response = $this->patch(route('focused-client.update'), [
        'client_id' => 999999,
    ]);

    $response->assertSessionHasErrors('client_id');

    $this->assertDatabaseHas('users', [
        'id'                => $user->id,
        'focused_client_id' => null,
    ]);
});
