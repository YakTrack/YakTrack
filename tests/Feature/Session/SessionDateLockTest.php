<?php

use App\Models\LockedSessionDate;
use App\Models\Session;
use Carbon\Carbon;

it('includes is_locked on days in the session index', function () {
    $this->usingTestDisplayTimezone();
    Carbon::setTestNow(Carbon::parse('2019-01-08 00:00:00'));

    $user = $this->actingAsUser();

    $session = Session::factory()->create([
        'started_at' => '2019-01-02 10:00:00',
        'ended_at'   => '2019-01-02 11:00:00',
    ]);

    LockedSessionDate::create([
        'user_id' => $user->id,
        'date'    => '2019-01-02',
    ]);

    $response = $this->get(route('session.index', ['per-page' => 100]));

    $days = collect($response->props()['days'])->keyBy('date');

    expect($days['2019-01-02']['is_locked'])->toBeTrue();
    expect($days['2019-01-02']['sessions'][0]['id'])->toBe($session->id);
});

it('can toggle a session date lock', function () {
    $this->actingAsUser();

    $response = $this->post(route('session-dates.toggle-lock', ['date' => '2019-01-02']));

    $response->assertRedirect();
    $response->assertSessionHas('success');

    expect(LockedSessionDate::query()->count())->toBe(1);

    $response = $this->post(route('session-dates.toggle-lock', ['date' => '2019-01-02']));

    $response->assertRedirect();
    expect(LockedSessionDate::query()->count())->toBe(0);
});

it('prevents updating a session on a locked date', function () {
    $user = $this->actingAsUser();

    $session = Session::factory()->create([
        'started_at' => '2019-01-02 10:00:00',
        'ended_at'   => '2019-01-02 11:00:00',
        'comment'    => 'Original',
    ]);

    LockedSessionDate::create([
        'user_id' => $user->id,
        'date'    => '2019-01-02',
    ]);

    $response = $this->patch(route('session.update', $session), [
        'started_at' => '2019-01-02 10:00:00',
        'ended_at'   => '2019-01-02 12:00:00',
        'comment'    => 'Updated',
        'is_billable' => 1,
    ]);

    $response->assertForbidden();
    expect($session->fresh()->comment)->toBe('Original');
});

it('prevents deleting a session on a locked date', function () {
    $user = $this->actingAsUser();

    $session = Session::factory()->create([
        'started_at' => '2019-01-02 10:00:00',
        'ended_at'   => '2019-01-02 11:00:00',
    ]);

    LockedSessionDate::create([
        'user_id' => $user->id,
        'date'    => '2019-01-02',
    ]);

    $response = $this->delete(route('session.destroy', $session));

    $response->assertForbidden();
    expect(Session::query()->whereKey($session->id)->exists())->toBeTrue();
});

it('prevents bulk updating sessions on locked dates', function () {
    $user = $this->actingAsUser();

    $session = Session::factory()->create([
        'started_at'  => '2019-01-02 10:00:00',
        'ended_at'    => '2019-01-02 11:00:00',
        'is_billable' => false,
    ]);

    LockedSessionDate::create([
        'user_id' => $user->id,
        'date'    => '2019-01-02',
    ]);

    $response = $this->patch(route('sessions.update'), [
        'sessions' => [
            $session->id => ['is_billable' => 1],
        ],
    ]);

    $response->assertForbidden();
    expect($session->fresh()->is_billable)->toBeFalse();
});

it('can bulk lock visible session dates', function () {
    $user = $this->actingAsUser();

    Session::factory()->create([
        'started_at' => '2019-01-02 10:00:00',
        'ended_at'   => '2019-01-02 11:00:00',
    ]);

    Session::factory()->create([
        'started_at' => '2019-01-03 10:00:00',
        'ended_at'   => '2019-01-03 11:00:00',
    ]);

    $response = $this->post(route('session-dates.lock-many'), [
        'dates' => ['2019-01-02', '2019-01-03'],
    ]);

    $response->assertRedirect();
    $response->assertSessionHas('success', '2 dates locked.');

    expect(LockedSessionDate::query()->where('user_id', $user->id)->count())->toBe(2);
});

it('can bulk unlock visible session dates', function () {
    $user = $this->actingAsUser();

    LockedSessionDate::create([
        'user_id' => $user->id,
        'date'    => '2019-01-02',
    ]);

    LockedSessionDate::create([
        'user_id' => $user->id,
        'date'    => '2019-01-03',
    ]);

    $response = $this->post(route('session-dates.unlock-many'), [
        'dates' => ['2019-01-02', '2019-01-03'],
    ]);

    $response->assertRedirect();
    $response->assertSessionHas('success', '2 dates unlocked.');

    expect(LockedSessionDate::query()->where('user_id', $user->id)->count())->toBe(0);
});

it('skips already locked dates when bulk locking', function () {
    $user = $this->actingAsUser();

    LockedSessionDate::create([
        'user_id' => $user->id,
        'date'    => '2019-01-02',
    ]);

    $response = $this->post(route('session-dates.lock-many'), [
        'dates' => ['2019-01-02', '2019-01-03'],
    ]);

    $response->assertRedirect();
    $response->assertSessionHas('success', '1 date locked.');

    expect(LockedSessionDate::query()->where('user_id', $user->id)->count())->toBe(2);
});

it('prevents opening the edit form for a session on a locked date', function () {
    $user = $this->actingAsUser();

    $session = Session::factory()->create([
        'started_at' => '2019-01-02 10:00:00',
        'ended_at'   => '2019-01-02 11:00:00',
    ]);

    LockedSessionDate::create([
        'user_id' => $user->id,
        'date'    => '2019-01-02',
    ]);

    $this->get(route('session.edit', $session))->assertForbidden();
    $this->get(route('session.edit-form', $session))->assertForbidden();
});
