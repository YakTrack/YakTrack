<?php

namespace Tests\Feature\Invoice\Session;

use App\Models\Invoice;
use App\Models\Session;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AttachSessionToInvoiceTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function a_user_can_attach_a_session_to_an_invoice_with_a_put_request()
    {
        $this->withoutExceptionHandling();

        $invoice = factory(Invoice::class)->create();

        $session = factory(Session::class)->create();

        $this->actingAsUser();

        $response = $this->put(route('invoice.session.update', [
            'invoice' => $invoice,
            'session' => $session,
        ]));

        $response->assertRedirect(
            route('invoice.session.index', ['invoice' => $invoice])
        );

        $this->assertTrue($session->fresh()->invoice->id === $invoice->id);
    }
}
