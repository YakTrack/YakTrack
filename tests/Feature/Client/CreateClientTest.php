<?php

it('can load the page to create a client', function () {
    $this->actingAsUser();

    $response = $this->get(route('client.create'));

    $response->assertSuccessful();
});

it('can submit a post request to create a client', function () {
    $this->actingAsUser();

    $response = $this->post(route('client.store'), $newClientDetails = [
        'name'  => 'Test Client',
        'email' => 'test@domain.com',
    ]);

    $response->assertRedirect(route('client.index'));

    $this->assertDatabaseHas('clients', $newClientDetails);
});

it('can create a client without an email address', function () {
    $this->actingAsUser();

    $response = $this->post(route('client.store'), $newClientDetails = [
        'name' => 'Test Client Without Email',
    ]);

    $response->assertRedirect(route('client.index'));

    $this->assertDatabaseHas('clients', $newClientDetails);
});
