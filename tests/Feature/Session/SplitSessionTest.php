<?php

use App\Models\Session;
use App\Models\Task;
use App\Support\DateTimeFormatter;
use Carbon\Carbon;

it('can split a completed session', function () {
    $task = Task::factory()->create();

    $session = Session::factory()->create([
        'task_id'     => $task->id,
        'started_at'  => app(DateTimeFormatter::class)->utcFormat('2023-01-01 09:00:00'),
        'ended_at'    => app(DateTimeFormatter::class)->utcFormat('2023-01-01 17:00:00'),
        'is_billable' => true,
        'comment'     => 'Original session comment',
    ]);

    $this->actingAsUser();

    $response = $this->post(route('session.split', $session->id), [
        'split_time' => '2023-01-01 13:00:00',
    ]);

    $response->assertRedirect(route('session.index'))
            ->assertSessionHas('success');

    // Check that the original session was updated in the database
    $this->assertDatabaseHas('sessions', [
        'id'         => $session->id,
        'started_at' => app(DateTimeFormatter::class)->utcFormat('2023-01-01 09:00:00'),
        'ended_at'   => app(DateTimeFormatter::class)->utcFormat('2023-01-01 13:00:00'),
    ]);

    // Check that a new session was created
    expect(Session::count())->toBe(2);

    $newSession = Session::where('id', '!=', $session->id)->first();

    // Check that the new session was created correctly in the database
    $this->assertDatabaseHas('sessions', [
        'id'         => $newSession->id,
        'started_at' => app(DateTimeFormatter::class)->utcFormat('2023-01-01 13:00:00'),
        'ended_at'   => app(DateTimeFormatter::class)->utcFormat('2023-01-01 17:00:00'),
    ]);

    // Check that the new session has the same properties as the original
    expect($newSession->task_id)->toBe($session->task_id);
    expect($newSession->is_billable)->toBeTrue();
    expect($newSession->comment)->toBe($session->comment);
});

it('requires split time to be within session duration', function () {
    $session = Session::factory()->create([
        'started_at' => Carbon::parse('2023-01-01 09:00:00'),
        'ended_at'   => Carbon::parse('2023-01-01 17:00:00'),
    ]);

    $this->actingAsUser();

    // Try to split with a time before session start
    $response = $this->post(route('session.split', $session->id), [
        'split_time' => '2023-01-01 08:00:00',
    ]);

    $response->assertSessionHasErrors(['split_time']);

    // Try to split with a time after session end
    $response = $this->post(route('session.split', $session->id), [
        'split_time' => '2023-01-01 18:00:00',
    ]);

    $response->assertSessionHasErrors(['split_time']);

    // Ensure no new sessions were created
    expect(Session::count())->toBe(1);
});

it('requires split time', function () {
    $session = Session::factory()->create([
        'started_at' => Carbon::parse('2023-01-01 09:00:00'),
        'ended_at'   => Carbon::parse('2023-01-01 17:00:00'),
    ]);

    $this->actingAsUser();

    $response = $this->post(route('session.split', $session->id), []);

    $response->assertSessionHasErrors(['split_time']);
    expect(Session::count())->toBe(1);
});

it('cannot split a running session', function () {
    $session = Session::factory()->create([
        'started_at' => app(DateTimeFormatter::class)->utcFormat('2023-01-01 09:00:00'),
        'ended_at'   => null, // Running session
    ]);

    $this->actingAsUser();

    $response = $this->post(route('session.split', $session->id), [
        'split_time' => '2023-01-01 13:00:00',
    ]);

    $response->assertRedirect(route('session.index'))
            ->assertSessionHas('error', 'Cannot split a running session. Please stop the session first.');
    expect(Session::count())->toBe(1);
});

it('can split a completed session into multiple segments', function () {
    $task = Task::factory()->create();

    $session = Session::factory()->create([
        'task_id'     => $task->id,
        'started_at'  => app(DateTimeFormatter::class)->utcFormat('2023-01-01 09:00:00'),
        'ended_at'    => app(DateTimeFormatter::class)->utcFormat('2023-01-01 17:00:00'),
        'is_billable' => true,
        'comment'     => 'Original session comment',
    ]);

    $this->actingAsUser();

    $response = $this->post(route('session.split', $session->id), [
        'segments' => [
            [
                'started_at' => '2023-01-01 09:00:00',
                'ended_at'   => '2023-01-01 11:00:00',
            ],
            [
                'started_at' => '2023-01-01 11:00:00',
                'ended_at'   => '2023-01-01 14:00:00',
            ],
            [
                'started_at' => '2023-01-01 14:00:00',
                'ended_at'   => '2023-01-01 17:00:00',
            ],
        ],
    ]);

    $response->assertRedirect(route('session.index'))
        ->assertSessionHas('success');

    expect(Session::count())->toBe(3);

    $this->assertDatabaseHas('sessions', [
        'id'         => $session->id,
        'started_at' => app(DateTimeFormatter::class)->utcFormat('2023-01-01 09:00:00'),
        'ended_at'   => app(DateTimeFormatter::class)->utcFormat('2023-01-01 11:00:00'),
    ]);

    $this->assertDatabaseHas('sessions', [
        'started_at' => app(DateTimeFormatter::class)->utcFormat('2023-01-01 11:00:00'),
        'ended_at'   => app(DateTimeFormatter::class)->utcFormat('2023-01-01 14:00:00'),
    ]);

    $this->assertDatabaseHas('sessions', [
        'started_at' => app(DateTimeFormatter::class)->utcFormat('2023-01-01 14:00:00'),
        'ended_at'   => app(DateTimeFormatter::class)->utcFormat('2023-01-01 17:00:00'),
    ]);
});

it('can assign sprint and task per segment when splitting', function () {
    $originalTask = Task::factory()->create();
    $firstTask = Task::factory()->create();
    $secondTask = Task::factory()->create();
    $firstSprint = \App\Models\Sprint::factory()->create();
    $secondSprint = \App\Models\Sprint::factory()->create();

    $session = Session::factory()->create([
        'task_id'     => $originalTask->id,
        'started_at'  => app(DateTimeFormatter::class)->utcFormat('2023-01-01 09:00:00'),
        'ended_at'    => app(DateTimeFormatter::class)->utcFormat('2023-01-01 13:00:00'),
    ]);

    $this->actingAsUser();

    $this->post(route('session.split', $session->id), [
        'segments' => [
            [
                'started_at' => '2023-01-01 09:00:00',
                'ended_at'   => '2023-01-01 11:00:00',
                'task_id'    => $firstTask->id,
                'sprint_id'  => $firstSprint->id,
            ],
            [
                'started_at' => '2023-01-01 11:00:00',
                'ended_at'   => '2023-01-01 13:00:00',
                'task_id'    => $secondTask->id,
                'sprint_id'  => $secondSprint->id,
            ],
        ],
    ])->assertRedirect(route('session.index'));

    $session->refresh();
    expect($session->task_id)->toBe($firstTask->id);
    expect($session->sprint_id)->toBe($firstSprint->id);

    $newSession = Session::where('id', '!=', $session->id)->first();
    expect($newSession->task_id)->toBe($secondTask->id);
    expect($newSession->sprint_id)->toBe($secondSprint->id);
});

it('rejects segments that do not cover the full session', function () {
    $session = Session::factory()->create([
        'started_at' => app(DateTimeFormatter::class)->utcFormat('2023-01-01 09:00:00'),
        'ended_at'   => app(DateTimeFormatter::class)->utcFormat('2023-01-01 17:00:00'),
    ]);

    $this->actingAsUser();

    $response = $this->post(route('session.split', $session->id), [
        'segments' => [
            [
                'started_at' => '2023-01-01 09:00:00',
                'ended_at'   => '2023-01-01 12:00:00',
            ],
            [
                'started_at' => '2023-01-01 12:00:00',
                'ended_at'   => '2023-01-01 16:00:00',
            ],
        ],
    ]);

    $response->assertSessionHasErrors(['segments.1.ended_at']);
    expect(Session::count())->toBe(1);
});
