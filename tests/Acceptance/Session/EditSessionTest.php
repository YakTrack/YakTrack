<?php

use App\Session;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\DatabaseMigrations;

class EditSessionTest extends TestCase
{
    use DatabaseMigrations;

    /** @test */
    public function a_user_can_end_a_session_with_a_patch_request()
    {
        $this->withoutExceptionHandling();

        $session = factory(Session::class)->create([
            'started_at' => '2018-01-01 00:00:00',
            'ended_at' => null,
        ]);

        $this->actingAsUser();

        Carbon::setTestNow(Carbon::parse('2018-01-01 12:34:56'));

        $this->patch(route('session.update', ['session' => $session]), [
            'ended_at' => '2018-01-01 12:34:56',
        ]);

        $this->shouldReturnJson();

        $this->seeJson([
            'id' => $session->id,
            'started_at' => [
                'date' => '2018-01-01 00:00:00.000000',
                'timezone' => 'UTC',
                'timezone_type' => 3,
            ],
            'ended_at' => [
                'date' => '2018-01-01 12:34:56.000000',
                'timezone' => 'UTC',
                'timezone_type' => 3,
            ],
        ]);

        Carbon::setTestNow();
    }

    /** @test */
    public function a_user_can_end_a_session_with_a_get_request()
    {
        Carbon::setTestNow(Carbon::parse('2018-01-01 00:10:00'));

        $this->withoutExceptionHandling();

        $session = factory(Session::class)->create([
            'started_at' => '2018-01-01 00:00:00',
            'ended_at' => null,
        ]);

        $this->actingAsUser();

        $this->visit(route('session.stop', ['session' => $session]));

        $this->seePageIs(route('session.index'));

        $this->seeInDatabase('sessions', [
            'started_at' => '2018-01-01 00:00:00',
            'ended_at' => '2018-01-01 00:10:00',
        ]);

        Carbon::setTestNow();
    }
}
