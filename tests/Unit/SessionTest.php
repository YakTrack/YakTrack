<?php

use App\Models\Session;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;

class SessionTest extends BrowserKitTestCase
{
    use RefreshDatabase;

    /** @test */
    public function stop_session_stops_the_session()
    {
        Carbon::setTestNow(Carbon::parse('2018-01-01 00:10:00'));

        $session = factory(Session::class)->create([
            'started_at' => '2018-01-01 00:00:00',
            'ended_at'   => null,
        ]);

        $this->assertTrue($session->isRunning());

        $session->stop();

        $this->assertFalse($session->isRunning());

        $this->seeInDatabase('sessions', [
            'id'         => $session->id,
            'started_at' => '2018-01-01 00:00:00',
            'ended_at'   => '2018-01-01 00:10:00',
        ]);

        Carbon::setTestNow();
    }

    /** @test */
    public function on_date_scope_returns_sessions_which_start_on_given_date_according_to_display_timezone()
    {
        $this->usingTestDisplayTimeZone();

        $startsAndEndsOnDate = factory(Session::class)->create([
            'started_at' => '2018-01-01 12:00:00',
            'ended_at'   => '2018-01-01 13:00:00',
        ]);

        $startsOnDateOnly = factory(Session::class)->create([
            'started_at' => '2018-01-01 12:00:00',
            'ended_at'   => '2018-01-02 12:30:00',
        ]);

        $endsOnDateOnly = factory(Session::class)->create([
            'started_at' => '2017-12-12 23:55:00',
            'ended_at'   => '2018-01-01 12:30:00',
        ]);

        $neitherStartsNorEndsOnDate = factory(Session::class)->create([
            'started_at' => '2017-01-01 11:00:00',
            'ended_at'   => '2017-01-01 11:30:00',
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
                    }),
                'Session '.$testCase[0]->id.' not found in dates on 2018-01-01'
            );
        }
    }

    /** @test */
    public function the_on_date_scope_does_not_return_dates_which_are_on_the_date_in_utc_but_not_display_timezone()
    {
        $session = Session::create(['started_at' => '2018-09-25 22:32:56']);

        $this->assertFalse(
            Session::onDate(Carbon::parse('2018-09-25 Australia/Sydney'))
                ->get()
                ->contains(function ($foundSession) use ($session) {
                    return $foundSession->id === $session->id;
                })
        );
    }

    /** @test */
    public function duration_in_seconds_attribute_returns_expected_value()
    {
        $session = factory(Session::class)->create([
            'started_at' => '2018-01-01 12:00:00',
            'ended_at'   => '2018-01-01 13:00:00',
        ]);

        $this->assertEquals(3600, $session->durationInSeconds);
    }
}
