<?php

namespace Tests\Feature\Invoice;

use App\Models\Invoice;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

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

        $response->assertViewIs('invoice.show');

        $response->assertSee($invoice->number);
    }
}
