<?php

namespace Tests\Feature\Session;

use App\Models\Session;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class StartSessionTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function a_user_can_start_a_session_from_now_with_a_post_request()
    {
        $previouslyRunningSession = factory(Session::class)->states('is_running')->create();

        $this->actingAsUser();

        Carbon::setTestNow(Carbon::parse('2018-01-01 12:34:56'));

        $response = $this->post(route('session.start'));

        $response->assertRedirect(route('session.index'));

        $this->assertDatabaseHas('sessions', [
            'started_at' => '2018-01-01 12:34:56',
        ]);

        $this->assertFalse($previouslyRunningSession->fresh()->isRunning());

        Carbon::setTestNow();
    }
}
