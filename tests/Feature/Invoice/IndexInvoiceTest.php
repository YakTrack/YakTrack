<?php

namespace Tests\Feature\Invoice;

use App\Models\Invoice;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class IndexInvoiceTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function a_user_can_see_a_list_of_invoices()
    {
        $this->withoutExceptionHandling();

        $invoice = factory(Invoice::class)->create();

        $this->actingAsUser();

        $response = $this->get(route('invoice.index'));

        $response->assertSuccessful();

        $response->assertSee($invoice->number);
    }
}
