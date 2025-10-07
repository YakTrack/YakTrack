<?php

use App\Models\Session;
use App\Models\Target;
use App\Statistics\Sessions;
use Carbon\Carbon;

it('includes total seconds worked in this weeks work sessions method', function () {
    Carbon::setTestNow('2018-09-25 00:00:00');

    $this->usingTestDisplayTimezone();

    Session::factory()->create([
        'started_at' => '2018-09-25 22:32:56',
        'ended_at'   => '2018-09-25 23:32:56',
    ]);

    expect(app(Sessions::class)->thisWeeksWorkSessions()[2]['totalSecondsWorked'])->toBe(3600);

    Carbon::setTestNow();
});

it('includes target in this weeks work sessions method', function () {
    Carbon::setTestNow('2018-09-25 00:00:00');

    $this->usingTestDisplayTimezone();

    Session::factory()->billable()->create([
        'started_at' => '2018-09-25 20:00:00',
        'ended_at'   => '2018-09-25 21:00:00',
    ]);

    Target::factory()->forDate()->inHours()->create([
        'starts_at'     => '2018-09-25 00:00:00',
        'value'         => 8,
        'billable_only' => 1,
    ]);

    expect(app(Sessions::class)->thisWeeksWorkSessions()[1]['totalSecondsTarget'])->toBe(8 * 3600);

    Carbon::setTestNow();
});
