<?php

use App\Models\Client;
use App\Models\Invoice;

it('can visit the create invoice page', function () {
    $this->withoutExceptionHandling();

    $this->actingAsUser();

    $response = $this->get(route('invoice.create'));

    $response->assertSuccessful();
});

it('can store an invoice with a post request', function () {
    $this->withoutExceptionHandling();

    $client = Client::factory()->create();

    $this->actingAsUser();

    $response = $this->post(route('invoice.store'), [
        'date'          => '2018-01-01',
        'due_date'      => '2018-01-08',
        'number'        => 'INV-001',
        'amount'        => '123.45',
        'client_id'     => $client->id,
        'description'   => 'Test description',
        'total_hours'   => 10,
    ]);

    $response->assertRedirect(route('invoice.index'));

    $this->assertDatabaseHas('invoices', [
        'date'          => '2018-01-01',
        'due_date'      => '2018-01-08',
        'number'        => 'INV-001',
        'amount'        => '12345',
        'client_id'     => $client->id,
        'description'   => 'Test description',
        'total_hours'   => 10,
        'is_sent'       => false,
        'is_paid'       => false,
    ]);
});

it('requires the invoice number to be unique', function () {
    $client = Client::factory()->create();
    Invoice::factory()->create([
        'number' => 'INV-001',
    ]);

    $this->actingAsUser();

    $response = $this->post(route('invoice.store'), [
        'date'          => '2018-01-01',
        'due_date'      => '2018-01-08',
        'number'        => 'INV-001',
        'amount'        => '123.45',
        'client_id'     => $client->id,
        'description'   => 'Test description',
        'total_hours'   => 10,
    ]);

    $response->assertRedirect();
    $response->assertSessionHasErrors('number');
});