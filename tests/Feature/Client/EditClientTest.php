<?php

use App\Models\Client;

it('can see the page to edit a client', function () {
    $client = Client::factory()->create();

    $this->actingAsUser();

    $response = $this->get(route('client.edit', ['client' => $client]));

    $response->assertSuccessful();
});

it('can submit a put request to update a client', function () {
    $client = Client::factory()->create();

    $this->actingAsUser();

    $response = $this->put(route('client.update', ['client' => $client]), $newClientDetails = [
        'name'  => 'New name',
        'email' => 'test@domain.com',
    ]);

    $response->assertRedirect(route('client.index'));

    $this->assertDatabaseHas('clients', array_merge(['id' => $client->id], $newClientDetails));
});