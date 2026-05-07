<?php

use App\Models\Client;
use App\Models\Invoice;
use App\Models\User;
use Laravel\Sanctum\Sanctum;

beforeEach(function () {
    Sanctum::actingAs(User::factory()->create());
});

it('returns 401 when unauthenticated', function () {
    app('auth')->forgetGuards();

    $this->getJson('/api/v1/invoices')
        ->assertUnauthorized();
});

it('can list invoices', function () {
    Invoice::factory()->count(3)->create();

    $this->getJson('/api/v1/invoices')
        ->assertSuccessful()
        ->assertJsonCount(3, 'data')
        ->assertJsonStructure([
            'data' => [['id', 'number', 'date', 'due_date', 'amount', 'is_paid', 'is_sent', 'created_at']],
        ]);
});

it('can create an invoice', function () {
    $client = Client::factory()->create();

    $this->postJson('/api/v1/invoices', [
        'number'    => 'INV-001',
        'date'      => '2026-01-15',
        'due_date'  => '2026-02-15',
        'amount'    => 1500.50,
        'client_id' => $client->id,
        'is_paid'   => false,
        'is_sent'   => true,
    ])
        ->assertCreated()
        ->assertJsonPath('data.number', 'INV-001')
        ->assertJsonPath('data.amount', 150050)
        ->assertJsonPath('data.is_sent', true);

    $this->assertDatabaseHas('invoices', ['number' => 'INV-001', 'amount' => 150050]);
});

it('validates unique invoice number', function () {
    Invoice::factory()->create(['number' => 'INV-DUP']);

    $this->postJson('/api/v1/invoices', [
        'number' => 'INV-DUP',
    ])->assertUnprocessable()
        ->assertJsonValidationErrors(['number']);
});

it('can create an invoice with minimal data', function () {
    $this->postJson('/api/v1/invoices', [])
        ->assertCreated();
});

it('can show an invoice', function () {
    $invoice = Invoice::factory()->create();

    $this->getJson("/api/v1/invoices/{$invoice->id}")
        ->assertSuccessful()
        ->assertJsonPath('data.id', $invoice->id)
        ->assertJsonStructure(['data' => ['id', 'number', 'date', 'client']]);
});

it('can update an invoice', function () {
    $invoice = Invoice::factory()->create();

    $this->putJson("/api/v1/invoices/{$invoice->id}", [
        'number'  => 'INV-UPDATED',
        'is_paid' => true,
    ])
        ->assertSuccessful()
        ->assertJsonPath('data.number', 'INV-UPDATED')
        ->assertJsonPath('data.is_paid', true);
});

it('can update invoice amount converting dollars to cents', function () {
    $invoice = Invoice::factory()->create();

    $this->putJson("/api/v1/invoices/{$invoice->id}", [
        'amount' => 99.99,
    ])->assertSuccessful()
        ->assertJsonPath('data.amount', 9999);
});

it('can delete an invoice', function () {
    $invoice = Invoice::factory()->create();

    $this->deleteJson("/api/v1/invoices/{$invoice->id}")
        ->assertNoContent();

    $this->assertDatabaseMissing('invoices', ['id' => $invoice->id]);
});
