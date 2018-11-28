<?php

namespace Tests\Feature\Session;

use App\Models\Invoice;
use App\Models\Session;
use App\Models\Sprint;
use App\Models\Task;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class EditSessionTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function a_user_can_load_the_page_to_edit_a_session()
    {
        $this->withoutExceptionHandling();

        $session = factory(Session::class)->create();

        $this->actingAsUser();

        $response = $this->get(route('session.edit', ['session' => $session]));

        $response->assertViewIs('session.edit');

        $response->assertSee($session->localStartedAt);
        $response->assertSee($session->localEndedAt);
    }

    /** @test */
    public function a_user_can_load_the_page_to_edit_a_session_in_pogress()
    {
        $this->withoutExceptionHandling();

        $session = factory(Session::class)->create([
            'ended_at' => null,
        ]);

        $this->actingAsUser();

        $response = $this->get(route('session.edit', ['session' => $session]));

        $response->assertViewIs('session.edit');

        $response->assertSee($session->localStartedAt);
    }

    /** @test */
    public function a_user_can_send_a_patch_request_to_edit_a_session()
    {
        // Default test display timezone is Asutralia/Sydney which corresponds to UTC+11 during
        // daylight savings in January
        $this->usingTestDisplayTimeZone();

        $this->withoutExceptionHandling();

        $session = factory(Session::class)->create([
            'started_at' => '2018-01-01 00:00:00',
            'ended_at'   => '2018-01-01 01:00:00',
        ]);

        $newTask     = factory(Task::class)->create();
        $newInvoice  = factory(Invoice::class)->create();
        $newSprint   = factory(Sprint::class)->create();

        $this->actingAsUser();

        $response = $this->patch(route('session.update', ['session' => $session]), [
            // Started at and ended at times ae submitted in display timezone
            'started_at' => '2018-01-01 12:00:00',
            // Started at and ended at times are submitted in display timezone
            'ended_at'   => '2018-01-01 13:00:00',
            'task_id'    => $newTask->id,
            'invoice_id' => $newInvoice->id,
            'sprint_id'  => $newSprint->id,
        ]);

        $response->assertRedirect(route('session.index'));

        $this->assertDatabaseHas('sessions', [
            'id'         => $session->id,
            // Australia/Sydney offset is UTC+11 in January, meaning that the corresponding UTC time for 12:00pm in display timezone
            // will be 1:00am on the same date
            'started_at' => '2018-01-01 01:00:00',
            // Australia/Sydney offset is UTC+11 in January, meaning that the corresponding UTC time for 1:00pm in display timezone
            // will be 2:00am on the same date
            'ended_at'   => '2018-01-01 02:00:00',
            'task_id'    => $newTask->id,
            'invoice_id' => $newInvoice->id,
            'sprint_id'  => $newSprint->id,
        ]);
    }

    /** @test */
    public function a_user_can_send_a_patch_request_to_edit_a_session_for_a_session_with_no_task_or_invoice()
    {
        $this->usingTestDisplayTimezone('UTC');
        $this->withoutExceptionHandling();

        $session = factory(Session::class)->create([
            'started_at' => '2018-01-01 00:00:00',
            'ended_at'   => null,
        ]);

        $newTask = factory(Task::class)->create();

        $this->actingAsUser();

        $response = $this->patch(route('session.update', ['session' => $session]), [
            'task_id'    => '',
            'started_at' => '2018-01-01 00:00:00',
            'ended_at'   => '',
        ]);

        $response->assertRedirect(route('session.index'));

        $this->assertDatabaseHas('sessions', [
            'id'            => $session->id,
            'task_id'       => null,
            'invoice_id'    => null,
            'started_at'    => '2018-01-01 00:00:00',
            'ended_at'      => null,
        ]);
    }

    /** @test */
    public function a_user_can_edit_a_session_with_a_json_patch_request()
    {
        $this->usingTestDisplayTimeZone('UTC');
        $this->withoutExceptionHandling();

        $session = factory(Session::class)->create([
            'started_at' => '2018-01-01 00:00:00',
            'ended_at'   => null,
        ]);

        $this->actingAsUser();

        $response = $this->json('patch', route('session.update', ['session' => $session]), [
            'started_at' => '2018-01-01 00:00:00',
            'ended_at'   => '2018-01-01 12:34:56',
        ]);

        $response->assertJson([
            'id'         => $session->id,
            'started_at' => [
                'date'          => '2018-01-01 00:00:00.000000',
                'timezone'      => 'UTC',
                'timezone_type' => 3,
            ],
            'ended_at' => [
                'date'          => '2018-01-01 12:34:56.000000',
                'timezone'      => 'UTC',
                'timezone_type' => 3,
            ],
        ]);

        Carbon::setTestNow();
    }
}
