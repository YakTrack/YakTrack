<?php

use App\Models\Client;

it('can see a single client', function () {
    $client = Client::factory()->create([
        'name' => 'Joseph O\'Conner',
    ]);

    $this->actingAsUser();

    $response = $this->get(route('client.show', ['client' => $client]));

    $response->assertSuccessful();

    $response->assertSee($client->name);
    $response->assertSee($client->email);
});