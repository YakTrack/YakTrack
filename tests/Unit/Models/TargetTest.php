<?php

use App\Models\Session;
use App\Models\Target;

it('returns expected target for find_for_date static method', function () {
    $target = Target::factory()->forDate()->create([
        'starts_at' => '2020-01-01 00:00:00',
    ]);

    expect(Target::findForDate('2020-01-01')->is($target))->toBeTrue();
});

it('returns the number of hours remaining for the target', function () {
    $target = Target::factory()->forDate()->inHours()->create([
        'starts_at' => '2020-01-01 00:00:00',
        'value'     => 8,
    ]);

    expect($target->hoursRemaining())->toBe(8.0);
});

it('returns the number of hours remaining for the target less any sessions', function () {
    $target = Target::factory()->forDate()->inHours()->create([
        'starts_at' => '2020-01-01 00:00:00',
        'value'     => 8,
    ]);

    Session::factory()->create([
        'started_at' => '2020-01-01 00:00:00',
        'ended_at'   => '2020-01-01 01:00:00',
    ]);

    expect($target->hoursRemaining())->toBe(7.0);
});

it('excludes non billable hours if billable_only is selected', function () {
    $target = Target::factory()->forDate()->inHours()->create([
        'starts_at'     => '2020-01-01 00:00:00',
        'value'         => 8,
        'billable_only' => 1,
    ]);

    Session::factory()->create([
        'started_at'  => '2020-01-01 00:00:00',
        'ended_at'    => '2020-01-01 01:00:00',
        'is_billable' => 0,
    ]);

    expect($target->hoursRemaining())->toBe(8.0);
});
