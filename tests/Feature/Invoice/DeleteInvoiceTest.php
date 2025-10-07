<?php

use App\Models\Invoice;
use App\Models\Session;

it('can delete an invoice', function () {
    $this->withoutExceptionHandling();

    $invoice = Invoice::factory()->create();
    $session = Session::factory()->create([
        'invoice_id' => $invoice->id,
    ]);

    $this->actingAsUser();

    $response = $this->delete(route('invoice.destroy', ['invoice' => $invoice]));

    $response->assertRedirect(route('invoice.index'));

    $this->assertDatabaseMissing('invoices', [
        'id' => $invoice->id,
    ]);
    $this->assertDatabaseMissing('sessions', [
        'id'         => $session->id,
        'invoice_id' => $invoice->id,
    ]);
});
