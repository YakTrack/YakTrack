<?php

use App\Session;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\DatabaseMigrations;

class SessionTest extends BrowserKitTestCase
{
    use DatabaseMigrations;

    /** @test */
    public function stop_session_stops_the_session()
    {
        Carbon::setTestNow(Carbon::parse('2018-01-01 00:10:00'));

        $session = factory(Session::class)->create([
            'started_at' => '2018-01-01 00:00:00',
            'ended_at' => null,
        ]);

        $this->assertTrue($session->isRunning());

        $session->stop();

        $this->assertFalse($session->isRunning());

        $this->seeInDatabase('sessions', [
            'id' => $session->id,
            'started_at' => '2018-01-01 00:00:00',
            'ended_at' => '2018-01-01 00:10:00',
        ]);

        Carbon::setTestNow();
    }

    /** @test */
    public function on_date_scope_returns_sessions_which_start_on_given_date()
    {
        $startsAndEndsOnDate = factory(Session::class)->create([
            'started_at' => '2018-01-01 12:00:00',
            'ended_at' => '2018-01-01 13:00:00',
        ]);

        $startsOnDateOnly = factory(Session::class)->create([
            'started_at' => '2018-01-01 23:55:00',
            'ended_at' => '2018-01-02 00:30:00',
        ]);

        $endsOnDateOnly = factory(Session::class)->create([
            'started_at' => '2017-12-12 23:55:00',
            'ended_at' => '2018-01-01 00:30:00',
        ]);

        $neitherStartsNorEndsOnDate = factory(Session::class)->create([
            'started_at' => '2017-01-01 12:00:00',
            'ended_at' => '2017-01-01 13:00:00',
        ]);

        foreach ([
            [$startsAndEndsOnDate, true],
            [$startsOnDateOnly, true],
            [$endsOnDateOnly, false],
            [$neitherStartsNorEndsOnDate, false],
        ] as $testCase) {
            $this->assertEquals(
                $testCase[1],
                Session::onDate(Carbon::parse('2018-01-01'))
                    ->get()
                    ->contains(function ($session) use ($testCase) {
                        return $session->id === $testCase[0]->id;
                    })
            );
        }
    }

    /** @test */
    public function duration_in_seconds_attribute_returns_expected_value()
    {
        $session = factory(Session::class)->create([
            'started_at' => '2018-01-01 12:00:00',
            'ended_at' => '2018-01-01 13:00:00',
        ]);

        $this->assertEquals(3600, $session->durationInSeconds);
    }
}
