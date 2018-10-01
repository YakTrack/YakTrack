<?php

namespace Tests\Unit\Statistics;

use App\Session;
use App\Statistics\Sessions;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SessionsTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function this_weeks_work_sessions_method_returns_expected_results()
    {
        Carbon::setTestNow('2018-09-25 00:00:00');

        $this->usingTestDisplayTimezone();

        $session = factory(Session::class)->create([
            'started_at' => '2018-09-25 22:32:56',
            'ended_at' => '2018-09-25 23:32:56',
        ]);

        $this->assertEquals('1:00:00', app(Sessions::class)->thisWeeksWorkSessions()[2]['totalTimeWorked']);
    }
}
