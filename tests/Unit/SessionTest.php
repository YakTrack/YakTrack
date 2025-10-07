<?php

use App\Models\Session;
use Carbon\Carbon;

it('stops the session when stop_session is called', function () {
    Carbon::setTestNow(Carbon::parse('2018-01-01 00:10:00'));

    $session = Session::factory()->create([
        'started_at' => '2018-01-01 00:00:00',
        'ended_at'   => null,
    ]);

    expect($session->isRunning())->toBeTrue();

    $session->stop();

    expect($session->isRunning())->toBeFalse();

    $this->seeInDatabase('sessions', [
        'id'         => $session->id,
        'started_at' => '2018-01-01 00:00:00',
        'ended_at'   => '2018-01-01 00:10:00',
    ]);

    Carbon::setTestNow();
});

it('returns sessions which start on given date according to display timezone', function () {
    $this->usingTestDisplayTimeZone();

    $startsAndEndsOnDate = Session::factory()->create([
        'started_at' => '2018-01-01 12:00:00',
        'ended_at'   => '2018-01-01 13:00:00',
    ]);

    $startsOnDateOnly = Session::factory()->create([
        'started_at' => '2018-01-01 12:00:00',
        'ended_at'   => '2018-01-02 12:30:00',
    ]);

    $endsOnDateOnly = Session::factory()->create([
        'started_at' => '2017-12-12 23:55:00',
        'ended_at'   => '2018-01-01 12:30:00',
    ]);

    $neitherStartsNorEndsOnDate = Session::factory()->create([
        'started_at' => '2017-01-01 11:00:00',
        'ended_at'   => '2017-01-01 11:30:00',
    ]);

    foreach ([
        [$startsAndEndsOnDate, true],
        [$startsOnDateOnly, true],
        [$endsOnDateOnly, false],
        [$neitherStartsNorEndsOnDate, false],
    ] as $testCase) {
        expect(Session::onDate(Carbon::parse('2018-01-01'))
            ->get()
            ->contains(function ($session) use ($testCase) {
                return $session->id === $testCase[0]->id;
            }))->toBe($testCase[1], 'Session '.$testCase[0]->id.' not found in dates on 2018-01-01');
    }
});

it('does not return dates which are on the date in utc but not display timezone', function () {
    $session = Session::create(['started_at' => '2018-09-25 22:32:56']);

    expect(Session::onDate(Carbon::parse('2018-09-25 Australia/Sydney'))
        ->get()
        ->contains(function ($foundSession) use ($session) {
            return $foundSession->id === $session->id;
        }))->toBeFalse();
});

it('returns sessions which started on the expected date for where_on_day_this_week_scope', function () {
    $this->usingTestDisplayTimeZone();

    Carbon::setTestNow('2020-11-12 09:40:00');

    $session = Session::create(['started_at' => '2020-11-12 09:20:00']);

    expect(Session::whereOnDayThisWeek('thursday')
        ->get()
        ->contains(function ($foundSession) use ($session) {
            return $foundSession->id === $session->id;
        }))->toBeTrue();
});

it('returns expected value for duration_in_seconds attribute', function () {
    $session = Session::factory()->create([
        'started_at' => '2018-01-01 12:00:00',
        'ended_at'   => '2018-01-01 13:00:00',
    ]);

    expect($session->durationInSeconds)->toBe(3600);
});