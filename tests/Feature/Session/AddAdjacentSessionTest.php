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

it('creates an adjacent session when the neighbouring session has no task, sprint or invoice', function () {
    $existing = Session::factory()->create([
        'started_at' => '2023-01-01 10:00:00',
        'ended_at'   => '2023-01-01 11:00:00',
        'task_id'    => null,
        'sprint_id'  => null,
        'invoice_id' => null,
    ]);

    $before = sessionsFromIndex()[$existing->id];

    $this->post(route('session.store'), [
        'started_at'          => $before['add_before_started_at'],
        'ended_at'            => $before['add_before_ended_at'],
        'task_id'             => null,
        'sprint_id'           => null,
        'invoice_id'          => null,
        'session_category_id' => null,
        'comment'             => null,
        'is_billable'         => true,
    ])
        ->assertSessionHasNoErrors()
        ->assertRedirect(route('session.index'));

    $this->assertDatabaseHas('sessions', [
        'started_at' => app(DateTimeFormatter::class)->utcFormat('2023-01-01 09:00:00'),
        'ended_at'   => app(DateTimeFormatter::class)->utcFormat('2023-01-01 10:00:00'),
        'task_id'    => null,
        'sprint_id'  => null,
        'invoice_id' => null,
    ]);
});

it('creates a session immediately after an existing session when related fields are null', function () {
    $existing = Session::factory()->create([
        'started_at' => '2023-01-01 10:00:00',
        'ended_at'   => '2023-01-01 11:00:00',
        'task_id'    => null,
        'sprint_id'  => null,
        'invoice_id' => null,
    ]);

    $after = sessionsFromIndex()[$existing->id];

    $this->post(route('session.store'), [
        'started_at'          => $after['add_after_started_at'],
        'ended_at'            => $after['add_after_ended_at'],
        'task_id'             => null,
        'sprint_id'           => null,
        'invoice_id'          => null,
        'session_category_id' => null,
        'comment'             => null,
        'is_billable'         => true,
    ])
        ->assertSessionHasNoErrors()
        ->assertRedirect(route('session.index'));

    $this->assertDatabaseHas('sessions', [
        'started_at' => app(DateTimeFormatter::class)->utcFormat('2023-01-01 11:00:00'),
        'ended_at'   => app(DateTimeFormatter::class)->utcFormat('2023-01-01 12:00:00'),
    ]);
});

it('creates a running session when ended_at is explicitly null', function () {
    $this->post(route('session.store'), [
        'started_at' => '2023-01-01 09:00:00',
        'ended_at'   => null,
    ])
        ->assertSessionHasNoErrors()
        ->assertRedirect(route('session.index'));

    $this->assertDatabaseHas('sessions', [
        'started_at' => app(DateTimeFormatter::class)->utcFormat('2023-01-01 09:00:00'),
        'ended_at'   => null,
    ]);
});

it('rejects a related id that does not exist', function (string $field) {
    $this->post(route('session.store'), [
        'started_at' => '2023-01-01 09:00:00',
        'ended_at'   => '2023-01-01 10:00:00',
        $field       => 999999,
    ])->assertSessionHasErrors($field);

    expect(Session::count())->toBe(0);
})->with(['task_id', 'sprint_id', 'invoice_id', 'session_category_id']);
