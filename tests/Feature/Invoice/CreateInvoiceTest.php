<?php

namespace Tests\Feature\Invoice;

use App\Models\Client;
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

        $client = factory(Client::class)->create();

        $this->actingAsUser();

        $response = $this->post(route('invoice.store'), [
            'date'          => '2018-01-01',
            'due_date'      => '2018-01-08',
            'number'        => 'INV-001',
            'amount'        => '123.45',
            'client_id'     => $client->id,
            'description'   => 'Test description',
            'total_hours'   => 10,
        ]);

        $response->assertRedirect(route('invoice.index'));

        $this->assertDatabaseHas('invoices', [
            'date'          => '2018-01-01',
            'due_date'      => '2018-01-08',
            'number'        => 'INV-001',
            'amount'        => '12345',
            'client_id'     => $client->id,
            'description'   => 'Test description',
            'total_hours'   => 10,
            'is_sent'       => false,
            'is_paid'       => false,
        ]);
    }
}
