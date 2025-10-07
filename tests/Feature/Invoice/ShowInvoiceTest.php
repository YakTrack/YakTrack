<?php

use App\Models\Invoice;

it('can view an invoice', function () {
    $this->withoutExceptionHandling();

    $invoice = Invoice::factory()->create([
        'amount' => 12345,
    ]);

    $this->actingAsUser();

    $response = $this->get(route('invoice.show', ['invoice' => $invoice]));

    $response->assertSuccessful();

    $response->assertSee($invoice->number);
    $response->assertSee(123.45);
});
