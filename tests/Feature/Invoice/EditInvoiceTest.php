<?php

namespace Tests\Feature\Invoice;

use App\Models\Invoice;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class EditInvoiceTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function a_user_can_edit_an_invoice()
    {
        $invoice = factory(Invoice::class)->create();
        $newInvoiceDetails = factory(Invoice::class)->make()->toArray();

        $this->actingAsUser();

        $response = $this->patch(route('invoice.update', ['invoice' => $invoice]), $newInvoiceDetails);

        $response->assertRedirect(route('invoice.index'));

        $this->assertDatabaseHas('invoices', [
            'id' => $invoice->id,
        ] + $newInvoiceDetails);
    }
}
