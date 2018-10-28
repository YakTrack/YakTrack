<?php

namespace Tests\Feature\Invoice;

use App\Models\Invoice;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class DeleteInvoiceTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function a_user_can_delete_an_invoice()
    {
        $this->withoutExceptionHandling();

        $invoice = factory(Invoice::class)->create();

        $this->actingAsUser();

        $response = $this->delete(route('invoice.destroy', ['invoice' => $invoice]));

        $response->assertRedirect(route('invoice.index'));

        $this->assertDatabaseMissing('invoices', [
            'id' => $invoice->id,
        ]);
    }
}
