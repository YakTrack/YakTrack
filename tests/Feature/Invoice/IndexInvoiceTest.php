<?php

namespace Tests\Feature\Invoice;

use App\Models\Invoice;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

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

        $response->assertViewIs('invoice.index');

        $response->assertSee($invoice->number);
    }
}
