<?php

use App\Session;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\DatabaseMigrations;

class CreateSessionTest extends BrowserKitTestCase
{
    use DatabaseMigrations;

    /** @test */
    public function a_user_can_start_a_session_with_a_post_request()
    {
        $this->withoutExceptionHandling();

        $this->actingAsUser();

        Carbon::setTestNow(Carbon::parse('2018-01-01 12:34:56'));

        $this->post(route('session.store'));

        $this->shouldReturnJson();

        $this->seeJson([
            'id'         => 1,
            'started_at' => [
                'date'          => '2018-01-01 12:34:56.000000',
                'timezone'      => 'UTC',
                'timezone_type' => 3,
            ],
        ]);

        Carbon::setTestNow();
    }

    /** @test */
    public function a_user_can_start_a_session_with_a_get_request()
    {
        $this->withoutExceptionHandling();

        $this->actingAsUser();

        Carbon::setTestNow(Carbon::parse('2018-01-01 12:34:56'));

        $this->visit(route('session.start'));

        $this->seePageIs(route('session.index'));

        $this->seeInDatabase('sessions', [
            'id'         => 1,
            'started_at' => '2018-01-01 12:34:56',
        ]);

        Carbon::setTestNow();
    }

    /** @test */
    public function when_a_user_starts_a_session_with_a_get_request_any_running_sessions_are_ended()
    {
        Carbon::setTestNow(Carbon::parse('2018-01-01 00:10:00'));

        $runningSession = factory(Session::class)->create([
            'started_at' => Carbon::parse('2018-01-01 00:00:00'),
            'ended_at'   => null,
        ]);

        $this->assertTrue($runningSession->isRunning());

        $this->withoutExceptionHandling();

        $this->actingAsUser();

        $this->visit(route('session.start'));

        $this->seePageIs(route('session.index'));

        $this->seeInDatabase('sessions', [
            'id'         => 1,
            'started_at' => '2018-01-01 00:00:00',
            'ended_at'   => '2018-01-01 00:10:00',
        ]);

        $this->seeInDatabase('sessions', [
            'id'         => 2,
            'started_at' => '2018-01-01 00:10:00',
            'ended_at'   => null,
        ]);

        $this->assertFalse($runningSession->fresh()->isRunning());

        Carbon::setTestNow();
    }
}
