<?php

namespace Tests\Feature\Invoice;

use App\Models\Client;
use App\Models\Invoice;
use App\Models\Session;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class EditInvoiceTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function a_user_can_view_the_edit_invoice_view()
    {
        $invoice = factory(Invoice::class)->create([
            'amount' => 12345,
        ]);

        $this->actingAsUser();

        $response = $this->get(route('invoice.edit', ['invoice' => $invoice]));

        $response->assertSee(123.45);
    }

    /** @test */
    public function a_user_can_edit_an_invoice()
    {
        $invoice = factory(Invoice::class)->create();
        $newClient = factory(Client::class)->create();

        $this->actingAsUser();

        $response = $this->patch(
            route('invoice.update', ['invoice' => $invoice]),
            $newInvoiceDetails = [
                'number'      => 'New Number',
                'date'        => '2018-01-01',
                'due_date'    => '2018-01-02',
                'amount'      => 123.45,
                'total_hours' => 123,
                'client_id'   => $newClient->id,
                'description' => 'New description',
                'is_paid'     => true,
                'is_sent'     => true,
            ]
        );

        $response->assertRedirect(route('invoice.index'));

        $this->assertDatabaseHas('invoices', array_merge([
            'id' => $invoice->id,
        ], $newInvoiceDetails, [
            'amount' => '12345',
        ]));
    }

    /** @test */
    public function a_user_can_attach_sessions_to_an_invoice_with_a_patch_json_request()
    {
        $this->withoutExceptionHandling();

        $invoice = factory(Invoice::class)->create();

        $sessions = factory(Session::class, 2)->create();

        $this->actingAsUser();

        $response = $this->json('patch', route('invoice.update', [
            'invoice' => $invoice,
        ]), [
            'sessions' => $sessions->pluck('id'),
        ]);

        $response->assertRedirect(route('invoice.index'));

        $sessions->each(function ($session) use ($invoice) {
            $this->assertTrue($session->fresh()->invoice_id == $invoice->id);
        });
    }

    /** @test */
    public function the_invoice_number_must_be_unique()
    {
        $client = factory(Client::class)->create();
        factory(Invoice::class)->create([
            'number' => 'INV-001',
        ]);
        $invoice = factory(Invoice::class)->create();

        $this->actingAsUser();

        $response = $this->patch(
            route('invoice.update', ['invoice' => $invoice]),
            [
                'number'      => 'INV-001',
                'date'        => '2018-01-01',
                'due_date'    => '2018-01-02',
                'amount'      => 123.45,
                'total_hours' => 123,
                'client_id'   => $client->id,
                'description' => 'New description',
                'is_paid'     => true,
                'is_sent'     => true,
            ]
        );

        $response->assertRedirect();
        $response->assertSessionHasErrors('number');
    }
}
