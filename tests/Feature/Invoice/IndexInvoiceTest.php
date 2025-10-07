<?php

use App\Models\Invoice;

it('can see a list of invoices', function () {
    $this->withoutExceptionHandling();

    $invoice = Invoice::factory()->create();

    $this->actingAsUser();

    $response = $this->get(route('invoice.index'));

    $response->assertSuccessful();

    $response->assertSee($invoice->number);
});