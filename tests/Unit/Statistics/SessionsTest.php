<?php

namespace Tests\Unit\Statistics;

use App\Models\Session;
use App\Models\Target;
use App\Statistics\Sessions;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SessionsTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function this_weeks_work_sessions_method_includes_total_seconds_worked()
    {
        Carbon::setTestNow('2018-09-25 00:00:00');

        $this->usingTestDisplayTimezone();

        $session = Session::factory()->create([
            'started_at' => '2018-09-25 22:32:56',
            'ended_at'   => '2018-09-25 23:32:56',
        ]);

        $this->assertEquals(3600, app(Sessions::class)->thisWeeksWorkSessions()[2]['totalSecondsWorked']);

        Carbon::setTestNow();
    }

    /** @test */
    public function this_weeks_work_sessions_method_includes_target()
    {
        Carbon::setTestNow('2018-09-25 00:00:00');

        $this->usingTestDisplayTimezone();

        $session = Session::factory()->billable()->create([
            'started_at' => '2018-09-25 20:00:00',
            'ended_at'   => '2018-09-25 21:00:00',
        ]);

        $target = Target::factory()->forDate()->inHours()->create([
            'starts_at'     => '2018-09-25 00:00:00',
            'value'         => 8,
            'billable_only' => 1,
        ]);

        $this->assertEquals(8 * 3600, app(Sessions::class)->thisWeeksWorkSessions()[1]['totalSecondsTarget']);

        Carbon::setTestNow();
    }
}
