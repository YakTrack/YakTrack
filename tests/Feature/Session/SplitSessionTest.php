<?php

namespace Tests\Feature\Session;

use App\Models\Session;
use App\Models\Task;
use App\Support\DateTimeFormatter;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SplitSessionTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function a_user_can_split_a_completed_session()
    {
        $task = Task::factory()->create();

        $session = Session::factory()->create([
            'task_id'     => $task->id,
            'started_at'  => app(DateTimeFormatter::class)->utcFormat('2023-01-01 09:00:00'),
            'ended_at'    => app(DateTimeFormatter::class)->utcFormat('2023-01-01 17:00:00'),
            'is_billable' => true,
            'comment'     => 'Original session comment',
        ]);

        $this->actingAsUser();

        $response = $this->post(route('session.split', $session->id), [
            'split_time' => '2023-01-01 13:00:00',
        ]);

        $response->assertRedirect(route('session.index'))
                ->assertSessionHas('success');

        // Check that the original session was updated in the database
        $this->assertDatabaseHas('sessions', [
            'id'         => $session->id,
            'started_at' => app(DateTimeFormatter::class)->utcFormat('2023-01-01 09:00:00'),
            'ended_at'   => app(DateTimeFormatter::class)->utcFormat('2023-01-01 13:00:00'),
        ]);

        // Check that a new session was created
        $this->assertEquals(2, Session::count());

        $newSession = Session::where('id', '!=', $session->id)->first();

        // Check that the new session was created correctly in the database
        $this->assertDatabaseHas('sessions', [
            'id'         => $newSession->id,
            'started_at' => app(DateTimeFormatter::class)->utcFormat('2023-01-01 13:00:00'),
            'ended_at'   => app(DateTimeFormatter::class)->utcFormat('2023-01-01 17:00:00'),
        ]);

        // Check that the new session has the same properties as the original
        $this->assertEquals($session->task_id, $newSession->task_id);
        $this->assertEquals($session->is_billable, $newSession->is_billable);
        $this->assertEquals($session->comment, $newSession->comment);
    }

    /** @test */
    public function split_time_must_be_within_session_duration()
    {
        $session = Session::factory()->create([
            'started_at' => Carbon::parse('2023-01-01 09:00:00'),
            'ended_at'   => Carbon::parse('2023-01-01 17:00:00'),
        ]);

        $this->actingAsUser();

        // Try to split with a time before session start
        $response = $this->post(route('session.split', $session->id), [
            'split_time' => '2023-01-01 08:00:00',
        ]);

        $response->assertSessionHasErrors(['split_time']);

        // Try to split with a time after session end
        $response = $this->post(route('session.split', $session->id), [
            'split_time' => '2023-01-01 18:00:00',
        ]);

        $response->assertSessionHasErrors(['split_time']);

        // Ensure no new sessions were created
        $this->assertEquals(1, Session::count());
    }

    /** @test */
    public function split_time_is_required()
    {
        $session = Session::factory()->create([
            'started_at' => Carbon::parse('2023-01-01 09:00:00'),
            'ended_at'   => Carbon::parse('2023-01-01 17:00:00'),
        ]);

        $this->actingAsUser();

        $response = $this->post(route('session.split', $session->id), []);

        $response->assertSessionHasErrors(['split_time']);
        $this->assertEquals(1, Session::count());
    }

    /** @test */
    public function cannot_split_a_running_session()
    {
        $session = Session::factory()->create([
            'started_at' => app(DateTimeFormatter::class)->utcFormat('2023-01-01 09:00:00'),
            'ended_at'   => null, // Running session
        ]);

        $this->actingAsUser();

        $response = $this->post(route('session.split', $session->id), [
            'split_time' => '2023-01-01 13:00:00',
        ]);

        $response->assertRedirect(route('session.index'))
                ->assertSessionHas('error', 'Cannot split a running session. Please stop the session first.');
        $this->assertEquals(1, Session::count());
    }
}
