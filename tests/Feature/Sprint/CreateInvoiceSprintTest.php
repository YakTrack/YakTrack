<?php

namespace Tests\Feature\Sprint;

use App\Models\Invoice;
use App\Models\Session;
use App\Models\Sprint;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CreateInvoiceSprintTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function a_user_can_create_an_invoice_from_a_sprint()
    {
        $this->actingAsUser();

        $this->withoutExceptionHandling();

        $sprint = factory(Sprint::class)->create();

        $sessions = factory(Session::class, 3)->states('billable')->create([
            'sprint_id'  => $sprint->id,
            'started_at' => Carbon::parse('Yesterday 9:00am'),
            'ended_at'   => Carbon::parse('Yesterday 10:00am'),
        ]);

        $response = $this->post(route('sprint.invoice.store', [
            'sprint' => $sprint,
        ]), [
            'date'          => $date = '2021-01-01',
            'due_date'      => $dueDate = '2021-01-08',
            'number'        => $number = 'INV-001',
            'amount'        => $amount = '123.45',
            'description'   => $description = 'Test description',
        ]);

        $response->assertRedirect(route('invoice.edit', [
            'invoice' => $invoice = Invoice::latest()->first(),
        ]));

        $this->assertDatabaseHas('invoices', [
            'date'          => $date,
            'due_date'      => $dueDate,
            'number'        => $number,
            'amount'        => $amount * 100,
            'description'   => $description,
            'client_id'     => $sprint->project->client->id,
            'total_hours'   => $sessions->totalDurationInHours(),
        ]);

        $sessions->each(function ($session) use ($invoice) {
            $this->assertTrue($invoice->sessions->contains($session));
        });
    }
}
