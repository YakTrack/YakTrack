<?php

namespace Tests\Feature\Invoice;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CreateInvoiceTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function a_user_can_visit_the_create_invoice_page()
    {
        $this->withoutExceptionHandling();

        $this->actingAsUser();

        $response = $this->get(route('invoice.create'));

        $response->assertViewIs('invoice.create');
    }

    /** @test */
    public function a_user_can_create_an_invoice()
    {
        $this->withoutExceptionHandling();

        $this->actingAsUser();

        $response = $this->post(route('invoice.store'), [
            'number'   => 'INV-001',
            'date'     => '2018-01-01',
            'amount'   => '123.45',
        ]);

        $response->assertRedirect(route('invoice.index'));

        $this->assertDatabaseHas('invoices', [
            'number'   => 'INV-001',
            'date'     => '2018-01-01',
            'amount'   => '12345',
        ]);
    }
}
