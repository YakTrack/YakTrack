<?php

namespace Tests\Feature\Invoice;

use App\Models\Invoice;
use App\Models\Session;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DeleteInvoiceTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function a_user_can_delete_an_invoice()
    {
        $this->withoutExceptionHandling();

        $invoice = factory(Invoice::class)->create();
        $session = factory(Session::class)->create([
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
    }
}
