<?php

use App\Models\Client;

it('can delete a client', function () {
    $client = Client::factory()->create();

    $this->actingAsUser();

    $response = $this->delete(route('client.destroy', [
        'client' => $client,
    ]));

    $response->assertRedirect(route('client.index'));

    $this->assertDatabaseMissing('clients', [
        'id' => $client->id,
    ]);
});
