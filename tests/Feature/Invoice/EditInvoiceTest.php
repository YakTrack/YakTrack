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
    public function a_user_can_edit_an_invoice()
    {
        $invoice   = factory(Invoice::class)->create();
        $newClient = factory(Client::class)->create();

        $this->actingAsUser();

        $response = $this->patch(
            route('invoice.update', ['invoice' => $invoice]),
            $newInvoiceDetails = [
                'number'      => 'New Number',
                'date'        => '2018-01-01',
                'amount'      => 123.45,
                'total_hours' => 123,
                'client_id'   => $newClient->id,
                'description' => 'New description',
                'is_paid'     => true,
                'is_sent'     => true,
            ]
        );

        $response->assertRedirect(route('invoice.index'));

        $this->assertDatabaseHas('invoices', [
            'id' => $invoice->id,
        ] + $newInvoiceDetails);
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

        $response->assertJson([
            'success' => true,
            'invoice' => $invoice->fresh(),
        ]);

        $sessions->each(function ($session) use ($invoice) {
            $this->assertTrue($session->fresh()->invoice_id == $invoice->id);
        });
    }
}
