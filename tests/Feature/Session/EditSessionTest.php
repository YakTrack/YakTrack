<?php

namespace Tests\Feature\Session;

use App\Models\Session;
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
    }

    /** @test */
    public function a_user_can_send_a_patch_request_to_edit_a_session()
    {
        $this->withoutExceptionHandling();

        $session = factory(Session::class)->create([
            'started_at' => '2018-01-01 00:00:00',
            'ended_at'   => null,
        ]);

        $newTask = factory(Task::class)->create();

        $this->actingAsUser();

        $response = $this->patch(route('session.update', ['session' => $session]), [
            'task_id'    => $newTask->id,
            'started_at' => '2018-01-01 00:00:00',
            'ended_at'   => '',
        ]);

        $response->assertRedirect(route('session.index'));

        $this->assertDatabaseHas('sessions', [
            'id'         => $session->id,
            'task_id'    => $newTask->id,
            'started_at' => '2018-01-01 00:00:00',
            'ended_at'   => null,
        ]);
    }

    /** @test */
    public function a_user_can_send_a_patch_request_to_edit_a_session_for_a_session_with_no_task()
    {
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
            'id'         => $session->id,
            'task_id'    => null,
            'started_at' => '2018-01-01 00:00:00',
            'ended_at'   => null,
        ]);
    }

    /** @test */
    public function a_user_can_edit_a_session_with_a_json_patch_request()
    {
        $this->withoutExceptionHandling();

        $session = factory(Session::class)->create([
            'started_at' => '2018-01-01 00:00:00',
            'ended_at'   => null,
        ]);

        $this->actingAsUser();

        Carbon::setTestNow(Carbon::parse('2018-01-01 12:34:56'));

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
