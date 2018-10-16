<?php

namespace Tests\Unit\Statistics;

use App\Models\Session;
use App\Statistics\Sessions;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SessionsTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function this_weeks_work_sessions_method_includes_total_time_worked()
    {
        Carbon::setTestNow('2018-09-25 00:00:00');

        $this->usingTestDisplayTimezone();

        $session = factory(Session::class)->create([
            'started_at' => '2018-09-25 22:32:56',
            'ended_at'   => '2018-09-25 23:32:56',
        ]);

        $this->assertEquals('1:00:00', app(Sessions::class)->thisWeeksWorkSessions()[2]['totalTimeWorked']);

        Carbon::setTestNow();
    }

    /** @test */
    public function this_weeks_work_sessions_method_includes_correct_dates()
    {
        Carbon::setTestNow('2018-10-16 21:12:14.757632 UTC');

        $this->usingTestDisplayTimezone();

        $this->assertEquals(Carbon::__set_state([
            'date'          => '2018-10-17 00:00:00.000000',
            'timezone_type' => 3,
            'timezone'      => 'Australia/Sydney',
        ]), app(Sessions::class)->thisWeeksWorkSessions()[2]['date']);

        Carbon::setTestNow();
    }
}
