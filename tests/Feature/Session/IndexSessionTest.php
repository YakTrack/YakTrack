<?php

use App\Models\Session;
use Carbon\Carbon;

it('redirects when no per page parameter is present', function () {
    $this->withoutExceptionHandling();

    $this->actingAsUser();

    $response = $this->get(route('session.index'));

    $response->assertRedirect(route('session.index', ['per-page' => 100]));
});

it('can load the session index page', function () {
    $this->withoutExceptionHandling();

    $this->actingAsUser();

    $response = $this->get(route('session.index', ['per-page' => 100]));

    $response->assertSuccessful();
});

it('can see a list of sessions filtered by start time', function () {
    $this->withoutExceptionHandling();
    $this->usingTestDisplayTimezone();

    Carbon::setTestNow(Carbon::parse('2019-01-08 00:00:00'));

    $tooEarlyForFilter = Session::factory()->create([
        'started_at' => '2019-01-01 00:00:00',
    ]);

    $recentEnoughForFilter = Session::factory()->create([
        'started_at' => '2019-01-02 00:00:00',
    ]);

    $tooLateForFilter = Session::factory()->create([
        'started_at' => '2019-01-03 00:00:00',
    ]);

    $this->actingAsUser();

    $response = $this->get(route('session.index', [
        'started-after'  => '2019-01-01 01:00:00',
        'started-before' => '2019-01-02 23:59:59',
        'per-page'       => 100,
    ]));

    $response->assertHasProp('days');

    $days = collect($response->props()['days'])->keyBy(function ($day) {
        return $day['date'];
    })->map(function ($day) {
        return collect($day['sessions']);
    });

    expect($days->contains(function ($sessions, $date) use ($recentEnoughForFilter) {
        return $sessions->contains(function ($session) use ($recentEnoughForFilter) {
            return $session['id'] == $recentEnoughForFilter->id;
        });
    }))->toBeTrue();

    expect($days->contains(function ($sessions, $date) use ($tooLateForFilter) {
        return $sessions->contains(function ($session) use ($tooLateForFilter) {
            return $session['id'] == $tooLateForFilter->id;
        });
    }))->toBeFalse();

    expect($days->contains(function ($sessions, $date) use ($tooEarlyForFilter) {
        return $sessions->contains(function ($session) use ($tooEarlyForFilter) {
            return $session['id'] == $tooEarlyForFilter->id;
        });
    }))->toBeFalse();
});