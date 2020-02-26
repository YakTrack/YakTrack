<?php

namespace Tests\Feature\Invoice;

use App\Models\Invoice;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ShowInvoiceTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function a_user_can_view_an_invoice()
    {
        $this->withoutExceptionHandling();

        $invoice = factory(Invoice::class)->create();

        $this->actingAsUser();

        $response = $this->get(route('invoice.show', ['invoice' => $invoice]));

        $response->assertSuccessful();

        $response->assertSee($invoice->number);
    }
}
