<?php

use App\Support\DateTimeFormatter;
use Carbon\Carbon;

beforeEach(function () {
    $this->usingTestDisplayTimezone();
});

it('returns the utc date time of the start of the week according to display timezone', function () {
    Carbon::setTestNow('2018-01-01 00:00:00');

    expect((new DateTimeFormatter())->startOfWeek())->toBe('2017-12-31 13:00:00');
});

it('returns the utc date time of the end of the week according to display timezone', function () {
    Carbon::setTestNow('2018-01-01 00:00:00');

    expect((new DateTimeFormatter())->endOfWeek())->toBe('2018-01-07 13:00:00');
});

it('returns expected value for to_utc_method', function () {
    $formatter = app(DateTimeFormatter::class);

    expect($formatter->toUTC(Carbon::parse('2018-01-01 00:00:00'))->format('Y-m-d H:i:s'))->toBe('2018-01-01 00:00:00');
    expect($formatter->toUTC(Carbon::parse('2018-01-01 00:00:00')->timezone('Australia/Sydney'))->format('Y-m-d H:i:s'))->toBe('2018-01-01 00:00:00');
});

it('returns expected value for utc_format_method', function () {
    $formatter = app(DateTimeFormatter::class);

    expect($formatter->utcFormat(Carbon::parse('2018-01-01T09:00:00+10:00')))->toBe('2017-12-31 23:00:00');
    expect($formatter->utcFormat(Carbon::parse('2018-01-01 00:00:00')->timezone('Australia/Sydney')))->toBe('2018-01-01 00:00:00');
});

it('returns the expected results for days_this_week_method', function () {
    Carbon::setTestNow(Carbon::parse('2018-10-16 21:12:14.757632 UTC'));

    $daysThisWeek = (new DateTimeFormatter())->daysThisWeek();

    expect($daysThisWeek)->toEqual(collect([
        0 => Carbon::__set_state([
            'date'          => '2018-10-15 00:00:00.000000',
            'timezone_type' => 3,
            'timezone'      => 'Australia/Sydney',
        ]),
        1 => Carbon::__set_state([
            'date'          => '2018-10-16 00:00:00.000000',
            'timezone_type' => 3,
            'timezone'      => 'Australia/Sydney',
        ]),
        2 => Carbon::__set_state([
            'date'          => '2018-10-17 00:00:00.000000',
            'timezone_type' => 3,
            'timezone'      => 'Australia/Sydney',
        ]),
        3 => Carbon::__set_state([
            'date'          => '2018-10-18 00:00:00.000000',
            'timezone_type' => 3,
            'timezone'      => 'Australia/Sydney',
        ]),
        4 => Carbon::__set_state([
            'date'          => '2018-10-19 00:00:00.000000',
            'timezone_type' => 3,
            'timezone'      => 'Australia/Sydney',
        ]),
        5 => Carbon::__set_state([
            'date'          => '2018-10-20 00:00:00.000000',
            'timezone_type' => 3,
            'timezone'      => 'Australia/Sydney',
        ]),
        6 => Carbon::__set_state([
            'date'          => '2018-10-21 00:00:00.000000',
            'timezone_type' => 3,
            'timezone'      => 'Australia/Sydney',
        ]),
    ]));

    expect($daysThisWeek[2]->isToday())->toBeTrue();

    Carbon::setTestNow();
});
