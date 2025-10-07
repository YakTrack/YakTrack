<?php

use App\Models\Client;
use App\Models\Invoice;
use App\Models\Session;

it('can view the edit invoice view', function () {
    $invoice = Invoice::factory()->create([
        'amount' => 12345,
    ]);

    $this->actingAsUser();

    $response = $this->get(route('invoice.edit', ['invoice' => $invoice]));

    $response->assertSee(123.45);
});

it('can edit an invoice', function () {
    $invoice = Invoice::factory()->create();
    $newClient = Client::factory()->create();

    $this->actingAsUser();

    $response = $this->patch(
        route('invoice.update', ['invoice' => $invoice]),
        $newInvoiceDetails = [
            'number'      => 'New Number',
            'date'        => '2018-01-01',
            'due_date'    => '2018-01-02',
            'amount'      => 123.45,
            'total_hours' => 123,
            'client_id'   => $newClient->id,
            'description' => 'New description',
            'is_paid'     => true,
            'is_sent'     => true,
        ]
    );

    $response->assertRedirect(route('invoice.index'));

    $this->assertDatabaseHas('invoices', array_merge([
        'id' => $invoice->id,
    ], $newInvoiceDetails, [
        'amount' => '12345',
    ]));
});

it('can attach sessions to an invoice with a patch json request', function () {
    $this->withoutExceptionHandling();

    $invoice = Invoice::factory()->create();

    $sessions = Session::factory()->count(2)->create();

    $this->actingAsUser();

    $response = $this->json('patch', route('invoice.update', [
        'invoice' => $invoice,
    ]), [
        'sessions' => $sessions->pluck('id'),
    ]);

    $response->assertRedirect(route('invoice.index'));

    $sessions->each(function ($session) use ($invoice) {
        expect($session->fresh()->invoice_id)->toBe($invoice->id);
    });
});

it('requires the invoice number to be unique', function () {
    $client = Client::factory()->create();
    Invoice::factory()->create([
        'number' => 'INV-001',
    ]);
    $invoice = Invoice::factory()->create();

    $this->actingAsUser();

    $response = $this->patch(
        route('invoice.update', ['invoice' => $invoice]),
        [
            'number'      => 'INV-001',
            'date'        => '2018-01-01',
            'due_date'    => '2018-01-02',
            'amount'      => 123.45,
            'total_hours' => 123,
            'client_id'   => $client->id,
            'description' => 'New description',
            'is_paid'     => true,
            'is_sent'     => true,
        ]
    );

    $response->assertRedirect();
    $response->assertSessionHasErrors('number');
});