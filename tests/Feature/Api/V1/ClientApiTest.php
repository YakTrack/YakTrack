<?php

use App\Models\Client;
use App\Models\User;
use Laravel\Sanctum\Sanctum;

beforeEach(function () {
    Sanctum::actingAs(User::factory()->create());
});

it('returns 401 when unauthenticated', function () {
    // Reset auth for this test
    app('auth')->forgetGuards();

    $this->getJson('/api/v1/clients')
        ->assertUnauthorized();
});

it('can list clients', function () {
    Client::factory()->count(3)->create();

    $this->getJson('/api/v1/clients')
        ->assertSuccessful()
        ->assertJsonCount(3, 'data')
        ->assertJsonStructure([
            'data' => [['id', 'name', 'email', 'is_billable', 'created_at', 'updated_at']],
            'links',
            'meta',
        ]);
});

it('can create a client', function () {
    $this->postJson('/api/v1/clients', [
        'name'  => 'Acme Corp',
        'email' => 'contact@acme.com',
    ])
        ->assertCreated()
        ->assertJsonPath('data.name', 'Acme Corp')
        ->assertJsonPath('data.email', 'contact@acme.com');

    $this->assertDatabaseHas('clients', ['name' => 'Acme Corp', 'email' => 'contact@acme.com']);
});

it('can create a client without an email', function () {
    $this->postJson('/api/v1/clients', ['name' => 'No Email Client'])
        ->assertCreated()
        ->assertJsonPath('data.name', 'No Email Client');
});

it('validates required fields when creating a client', function () {
    $this->postJson('/api/v1/clients', [])
        ->assertUnprocessable()
        ->assertJsonValidationErrors(['name']);
});

it('validates email format when creating a client', function () {
    $this->postJson('/api/v1/clients', [
        'name'  => 'Bad Email Client',
        'email' => 'not-an-email',
    ])
        ->assertUnprocessable()
        ->assertJsonValidationErrors(['email']);
});

it('can show a client', function () {
    $client = Client::factory()->create(['name' => 'Show Me']);

    $this->getJson("/api/v1/clients/{$client->id}")
        ->assertSuccessful()
        ->assertJsonPath('data.name', 'Show Me');
});

it('returns 404 for a non-existent client', function () {
    $this->getJson('/api/v1/clients/99999')
        ->assertNotFound();
});

it('can update a client', function () {
    $client = Client::factory()->create(['name' => 'Old Name']);

    $this->putJson("/api/v1/clients/{$client->id}", ['name' => 'New Name'])
        ->assertSuccessful()
        ->assertJsonPath('data.name', 'New Name');

    $this->assertDatabaseHas('clients', ['id' => $client->id, 'name' => 'New Name']);
});

it('can delete a client', function () {
    $client = Client::factory()->create();

    $this->deleteJson("/api/v1/clients/{$client->id}")
        ->assertNoContent();

    $this->assertDatabaseMissing('clients', ['id' => $client->id]);
});
