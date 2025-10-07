<?php

use App\Models\Client;

it('can see a list of clients', function () {
    $client = Client::factory()->create();

    $this->actingAsUser();

    $response = $this->get(route('client.index'));

    $response->assertSuccessful();

    $response->assertSee(e($client->name));
});
