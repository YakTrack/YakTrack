<?php

namespace Tests\Feature\Session;

use App\Models\Invoice;
use App\Models\Session;
use App\Models\Sprint;
use App\Models\Task;
use App\Support\DateTimeFormatter;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CreateSessionTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function a_user_can_create_a_session_with_a_post_request()
    {
        $invoice = factory(Invoice::class)->create();
        $sprint = factory(Sprint::class)->create();
        $task = factory(Task::class)->create();

        $this->withoutExceptionHandling();

        $this->actingAsUser();

        $response = $this->post(route('session.store'), [
            'started_at'    => '2018-01-01 12:34:56',
            'ended_at'      => '2018-01-01 12:34:57',
            'sprint_id'     => $sprint->id,
            'invoice_id'    => $invoice->id,
            'task_id'       => $task->id,
        ]);

        $response->assertRedirect(route('session.index'));

        $this->assertDatabaseHas('sessions', [
            'started_at'    => app(DateTimeFormatter::class)->utcFormat('2018-01-01 12:34:56'),
            'ended_at'      => app(DateTimeFormatter::class)->utcFormat('2018-01-01 12:34:57'),
            'sprint_id'     => $sprint->id,
            'invoice_id'    => $invoice->id,
            'task_id'       => $task->id,
        ]);
    }

    /** @test */
    public function a_user_can_create_a_session_with_a_post_request_with_the_minimum_required_fields()
    {
        $previouslyRunningSession = factory(Session::class)->states('is_running')->create();

        $this->withoutExceptionHandling();

        $this->actingAsUser();

        $response = $this->post(route('session.store'), [
            'started_at' => '2018-01-01 12:34:56',
        ]);

        $response->assertRedirect(route('session.index'));

        $this->assertDatabaseHas('sessions', [
            'started_at' => app(DateTimeFormatter::class)->utcFormat('2018-01-01 12:34:56'),
            'ended_at' => null,
        ]);

        $this->assertFalse($previouslyRunningSession->isRunning);
    }
}
