<?php

use App\Models\Session;
use App\Support\DateTimeFormatter;

/**
 * @return \Illuminate\Support\Collection<int, array<string, mixed>>
 */
function sessionsFromIndex(): \Illuminate\Support\Collection
{
    $response = test()->get(route('session.index', ['per-page' => 100]));

    $response->assertSuccessful();

    return collect($response->props('days'))
        ->flatMap(fn ($day) => $day['sessions'])
        ->keyBy('id');
}

beforeEach(function () {
    $this->usingTestDisplayTimeZone('UTC');
    $this->actingAsUser();
});

it('hides add-before and add-after when a session is touching on that side', function () {
    $earlier = Session::factory()->create([
        'started_at' => '2023-01-01 09:00:00',
        'ended_at'   => '2023-01-01 10:00:00',
    ]);

    $later = Session::factory()->create([
        'started_at' => '2023-01-01 10:00:00',
        'ended_at'   => '2023-01-01 11:00:00',
    ]);

    $sessions = sessionsFromIndex();

    expect($sessions[$earlier->id]['can_add_session_before'])->toBeTrue();
    expect($sessions[$earlier->id]['can_add_session_after'])->toBeFalse();

    expect($sessions[$later->id]['can_add_session_before'])->toBeFalse();
    expect($sessions[$later->id]['can_add_session_after'])->toBeTrue();
});

it('allows adding a session in the gap between two sessions and fills the gap by default', function () {
    $earlier = Session::factory()->create([
        'started_at' => '2023-01-01 09:00:00',
        'ended_at'   => '2023-01-01 10:00:00',
    ]);

    $later = Session::factory()->create([
        'started_at' => '2023-01-01 11:00:00',
        'ended_at'   => '2023-01-01 12:00:00',
    ]);

    $sessions = sessionsFromIndex();

    expect($sessions[$earlier->id]['can_add_session_after'])->toBeTrue();
    expect($sessions[$earlier->id]['add_after_started_at'])->toBe('2023-01-01 10:00:00');
    expect($sessions[$earlier->id]['add_after_ended_at'])->toBe('2023-01-01 11:00:00');

    expect($sessions[$later->id]['can_add_session_before'])->toBeTrue();
    expect($sessions[$later->id]['add_before_started_at'])->toBe('2023-01-01 10:00:00');
    expect($sessions[$later->id]['add_before_ended_at'])->toBe('2023-01-01 11:00:00');
});

it('defaults to a one hour duration when there is no neighbouring session', function () {
    $session = Session::factory()->create([
        'started_at' => '2023-01-01 09:00:00',
        'ended_at'   => '2023-01-01 10:00:00',
    ]);

    $sessions = sessionsFromIndex();

    expect($sessions[$session->id]['can_add_session_before'])->toBeTrue();
    expect($sessions[$session->id]['add_before_started_at'])->toBe('2023-01-01 08:00:00');
    expect($sessions[$session->id]['add_before_ended_at'])->toBe('2023-01-01 09:00:00');

    expect($sessions[$session->id]['can_add_session_after'])->toBeTrue();
    expect($sessions[$session->id]['add_after_started_at'])->toBe('2023-01-01 10:00:00');
    expect($sessions[$session->id]['add_after_ended_at'])->toBe('2023-01-01 11:00:00');
});

it('allows a session to be added before a running session but not after', function () {
    $session = Session::factory()->running()->create([
        'started_at' => '2023-01-01 09:00:00',
    ]);

    $sessions = sessionsFromIndex();

    expect($sessions[$session->id]['can_add_session_before'])->toBeTrue();
    expect($sessions[$session->id]['add_before_started_at'])->toBe('2023-01-01 08:00:00');
    expect($sessions[$session->id]['add_before_ended_at'])->toBe('2023-01-01 09:00:00');

    expect($sessions[$session->id]['can_add_session_after'])->toBeFalse();
    expect($sessions[$session->id]['add_after_started_at'])->toBeNull();
    expect($sessions[$session->id]['add_after_ended_at'])->toBeNull();
});

it('creates a session immediately before an existing session via the store endpoint', function () {
    $existing = Session::factory()->create([
        'started_at' => '2023-01-01 10:00:00',
        'ended_at'   => '2023-01-01 11:00:00',
    ]);

    $before = sessionsFromIndex()[$existing->id];

    $this->post(route('session.store'), [
        'started_at' => $before['add_before_started_at'],
        'ended_at'   => $before['add_before_ended_at'],
    ])->assertRedirect(route('session.index'));

    $this->assertDatabaseHas('sessions', [
        'started_at' => app(DateTimeFormatter::class)->utcFormat('2023-01-01 09:00:00'),
        'ended_at'   => app(DateTimeFormatter::class)->utcFormat('2023-01-01 10:00:00'),
    ]);

    // The existing session now has a session touching it, so add-before is hidden.
    expect(sessionsFromIndex()[$existing->id]['can_add_session_before'])->toBeFalse();
});
