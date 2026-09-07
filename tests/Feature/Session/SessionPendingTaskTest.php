<?php

use App\Models\LockedSessionDate;
use App\Models\Project;
use App\Models\Session;
use App\Models\SessionPendingTask;
use App\Models\Task;
use App\Models\TaskStatus;
use App\Support\DateTimeFormatter;
use Illuminate\Support\Facades\DB;

it('can link a pending task to a running session', function () {
    $session = Session::factory()->running()->create();
    $task = Task::factory()->create();

    $this->actingAsUser();

    $response = $this->postJson(route('session.pending-tasks.store', $session), [
        'task_id' => $task->id,
    ]);

    $response->assertSuccessful();

    $this->assertDatabaseHas('session_pending_tasks', [
        'session_id' => $session->id,
        'task_id'    => $task->id,
    ]);
});

it('can unlink a pending task from a session', function () {
    $session = Session::factory()->running()->create();
    $task = Task::factory()->create();

    SessionPendingTask::factory()->create([
        'session_id' => $session->id,
        'task_id'    => $task->id,
    ]);

    $this->actingAsUser();

    $response = $this->deleteJson(route('session.pending-tasks.destroy', [$session, $task]));

    $response->assertSuccessful();

    $this->assertDatabaseMissing('session_pending_tasks', [
        'session_id' => $session->id,
        'task_id'    => $task->id,
    ]);
});

it('does not create a link when reading the index of a finished session with a task', function () {
    $task = Task::factory()->create();
    $session = Session::factory()->create([
        'task_id' => $task->id,
    ]);

    $this->actingAsUser();

    $response = $this->getJson(route('session.pending-tasks.index', $session));

    $response->assertSuccessful()
        ->assertJsonCount(0, 'pending_tasks');

    expect($session->pendingTasks()->count())->toBe(0);
});

it('does not seed a pending link when the session has no task', function () {
    $session = Session::factory()->running()->create([
        'task_id' => null,
    ]);

    $this->actingAsUser();

    $response = $this->getJson(route('session.pending-tasks.index', $session));

    $response->assertSuccessful()
        ->assertJsonCount(0, 'pending_tasks');

    expect($session->pendingTasks()->count())->toBe(0);
});

it('does not create a duplicate when linking the same task twice', function () {
    $session = Session::factory()->running()->create();
    $task = Task::factory()->create();

    $this->actingAsUser();

    $this->postJson(route('session.pending-tasks.store', $session), ['task_id' => $task->id])
        ->assertSuccessful();
    $this->postJson(route('session.pending-tasks.store', $session), ['task_id' => $task->id])
        ->assertSuccessful();

    expect(SessionPendingTask::where('session_id', $session->id)->where('task_id', $task->id)->count())->toBe(1);
});

it('leaves the session unchanged when stopping with no pending tasks', function () {
    $task = Task::factory()->create();
    $session = Session::factory()->running()->create([
        'task_id' => $task->id,
    ]);

    $this->actingAsUser();

    $response = $this->post(route('session.stop'));

    $response->assertRedirect(route('session.index'))
        ->assertSessionMissing('splitPendingSessionId');

    $session->refresh();
    expect($session->isRunning())->toBeFalse();
    expect($session->task_id)->toBe($task->id);
    expect($session->pendingTasks()->count())->toBe(0);
});

it('assigns the single pending task and deletes the link when stopping', function () {
    $originalTask = Task::factory()->create();
    $pendingTask = Task::factory()->create();

    $session = Session::factory()->running()->create([
        'task_id' => $originalTask->id,
    ]);

    SessionPendingTask::factory()->create([
        'session_id' => $session->id,
        'task_id'    => $pendingTask->id,
    ]);

    $this->actingAsUser();

    $response = $this->post(route('session.stop'));

    $response->assertRedirect(route('session.index'))
        ->assertSessionMissing('splitPendingSessionId');

    $session->refresh();
    expect($session->isRunning())->toBeFalse();
    expect($session->task_id)->toBe($pendingTask->id);
    expect($session->pendingTasks()->count())->toBe(0);
});

it('keeps pending links and signals a split when stopping with two or more pending tasks', function () {
    $session = Session::factory()->running()->create();
    $firstTask = Task::factory()->create();
    $secondTask = Task::factory()->create();

    SessionPendingTask::factory()->create(['session_id' => $session->id, 'task_id' => $firstTask->id]);
    SessionPendingTask::factory()->create(['session_id' => $session->id, 'task_id' => $secondTask->id]);

    $this->actingAsUser();

    $response = $this->post(route('session.stop'));

    $response->assertRedirect(route('session.index'))
        ->assertSessionHas('splitPendingSessionId', $session->id);

    $session->refresh();
    expect($session->isRunning())->toBeFalse();
    expect($session->pendingTasks()->count())->toBe(2);
});

it('consumes the pending links after a successful split', function () {
    $firstTask = Task::factory()->create();
    $secondTask = Task::factory()->create();

    $session = Session::factory()->create([
        'started_at' => app(DateTimeFormatter::class)->utcFormat('2023-01-01 09:00:00'),
        'ended_at'   => app(DateTimeFormatter::class)->utcFormat('2023-01-01 17:00:00'),
    ]);

    SessionPendingTask::factory()->create(['session_id' => $session->id, 'task_id' => $firstTask->id]);
    SessionPendingTask::factory()->create(['session_id' => $session->id, 'task_id' => $secondTask->id]);

    $this->actingAsUser();

    $response = $this->post(route('session.split', $session->id), [
        'from_pending_tasks' => true,
        'segments'           => [
            [
                'started_at' => '2023-01-01 09:00:00',
                'ended_at'   => '2023-01-01 13:00:00',
                'task_id'    => $firstTask->id,
            ],
            [
                'started_at' => '2023-01-01 13:00:00',
                'ended_at'   => '2023-01-01 17:00:00',
                'task_id'    => $secondTask->id,
            ],
        ],
    ]);

    $response->assertRedirect(route('session.index'))
        ->assertSessionHas('success');

    expect($session->pendingTasks()->count())->toBe(0);
    expect(SessionPendingTask::where('session_id', $session->id)->count())->toBe(0);
});

it('surfaces pending tasks for a finished session on the index payload', function () {
    $this->withoutExceptionHandling();
    $this->usingTestDisplayTimeZone();

    $firstTask = Task::factory()->create();
    $secondTask = Task::factory()->create();

    $session = Session::factory()->create([
        'started_at' => app(DateTimeFormatter::class)->utcFormat('2023-01-01 09:00:00'),
        'ended_at'   => app(DateTimeFormatter::class)->utcFormat('2023-01-01 17:00:00'),
    ]);

    SessionPendingTask::factory()->create(['session_id' => $session->id, 'task_id' => $firstTask->id]);
    SessionPendingTask::factory()->create(['session_id' => $session->id, 'task_id' => $secondTask->id]);

    $this->actingAsUser();

    $response = $this->get(route('session.index', ['per-page' => 100]));

    $response->assertHasProp('days');

    $sessions = collect($response->props()['days'])->flatMap(fn ($day) => $day['sessions']);
    $payload = $sessions->firstWhere('id', $session->id);

    expect($payload)->not->toBeNull();
    expect($payload['pending_tasks'])->toHaveCount(2);
    expect(collect($payload['pending_tasks'])->pluck('task_id')->sort()->values()->all())
        ->toBe(collect([$firstTask->id, $secondTask->id])->sort()->values()->all());
});

it('carries the split signal through to the rendered index after stopping', function () {
    $this->usingTestDisplayTimeZone();

    $session = Session::factory()->running()->create();

    SessionPendingTask::factory()->create(['session_id' => $session->id, 'task_id' => Task::factory()->create()->id]);
    SessionPendingTask::factory()->create(['session_id' => $session->id, 'task_id' => Task::factory()->create()->id]);

    $this->actingAsUser();

    $response = $this->followingRedirects()->post(route('session.stop'));

    $response->assertSuccessful()
        ->assertPropValue('flash.splitPendingSessionId', $session->id);
});

it('leaves no pending links behind after a legacy split_time split', function () {
    $task = Task::factory()->create();

    $session = Session::factory()->create([
        'task_id'    => $task->id,
        'started_at' => app(DateTimeFormatter::class)->utcFormat('2023-01-01 09:00:00'),
        'ended_at'   => app(DateTimeFormatter::class)->utcFormat('2023-01-01 17:00:00'),
    ]);

    SessionPendingTask::factory()->create(['session_id' => $session->id, 'task_id' => Task::factory()->create()->id]);
    SessionPendingTask::factory()->create(['session_id' => $session->id, 'task_id' => Task::factory()->create()->id]);

    $this->actingAsUser();

    $this->post(route('session.split', $session->id), [
        'split_time' => '2023-01-01 13:00:00',
    ])->assertRedirect(route('session.index'))->assertSessionHas('success');

    expect(SessionPendingTask::where('session_id', $session->id)->count())->toBe(0);
});

it('keeps a pending link that is not represented in the submitted segments', function () {
    $firstTask = Task::factory()->create();
    $secondTask = Task::factory()->create();
    $unrepresentedTask = Task::factory()->create();

    $session = Session::factory()->create([
        'started_at' => app(DateTimeFormatter::class)->utcFormat('2023-01-01 09:00:00'),
        'ended_at'   => app(DateTimeFormatter::class)->utcFormat('2023-01-01 17:00:00'),
    ]);

    SessionPendingTask::factory()->create(['session_id' => $session->id, 'task_id' => $firstTask->id]);
    SessionPendingTask::factory()->create(['session_id' => $session->id, 'task_id' => $secondTask->id]);
    SessionPendingTask::factory()->create(['session_id' => $session->id, 'task_id' => $unrepresentedTask->id]);

    $this->actingAsUser();

    $this->post(route('session.split', $session->id), [
        'from_pending_tasks' => true,
        'segments'           => [
            [
                'started_at' => '2023-01-01 09:00:00',
                'ended_at'   => '2023-01-01 13:00:00',
                'task_id'    => $firstTask->id,
            ],
            [
                'started_at' => '2023-01-01 13:00:00',
                'ended_at'   => '2023-01-01 17:00:00',
                'task_id'    => $secondTask->id,
            ],
        ],
    ])->assertRedirect(route('session.index'))->assertSessionHas('success');

    expect($session->pendingTasks()->pluck('task_id')->all())->toBe([$unrepresentedTask->id]);
});

it('rejects a segments split whose tasks are not linked to the session', function () {
    $firstTask = Task::factory()->create();
    $secondTask = Task::factory()->create();
    $unlinkedTask = Task::factory()->create();

    $session = Session::factory()->create([
        'started_at' => app(DateTimeFormatter::class)->utcFormat('2023-01-01 09:00:00'),
        'ended_at'   => app(DateTimeFormatter::class)->utcFormat('2023-01-01 17:00:00'),
    ]);

    SessionPendingTask::factory()->create(['session_id' => $session->id, 'task_id' => $firstTask->id]);
    SessionPendingTask::factory()->create(['session_id' => $session->id, 'task_id' => $secondTask->id]);

    $this->actingAsUser();

    $this->post(route('session.split', $session->id), [
        'from_pending_tasks' => true,
        'segments'           => [
            [
                'started_at' => '2023-01-01 09:00:00',
                'ended_at'   => '2023-01-01 13:00:00',
                'task_id'    => $firstTask->id,
            ],
            [
                'started_at' => '2023-01-01 13:00:00',
                'ended_at'   => '2023-01-01 17:00:00',
                'task_id'    => $unlinkedTask->id,
            ],
        ],
    ])->assertRedirect(route('session.index'))->assertSessionHas('error');

    expect(Session::count())->toBe(1);
    expect($session->pendingTasks()->count())->toBe(2);
});

it('scopes destroy to the given session', function () {
    $task = Task::factory()->create();

    $sessionA = Session::factory()->running()->create();
    $sessionB = Session::factory()->running()->create();

    SessionPendingTask::factory()->create(['session_id' => $sessionA->id, 'task_id' => $task->id]);
    SessionPendingTask::factory()->create(['session_id' => $sessionB->id, 'task_id' => $task->id]);

    $this->actingAsUser();

    $this->deleteJson(route('session.pending-tasks.destroy', [$sessionA, $task]))->assertSuccessful();

    $this->assertDatabaseMissing('session_pending_tasks', ['session_id' => $sessionA->id, 'task_id' => $task->id]);
    $this->assertDatabaseHas('session_pending_tasks', ['session_id' => $sessionB->id, 'task_id' => $task->id]);
});

it('can link and unlink a task on a finished editable session', function () {
    $originalTask = Task::factory()->create();
    $extraTask = Task::factory()->create();

    $session = Session::factory()->create([
        'task_id'    => $originalTask->id,
        'started_at' => app(DateTimeFormatter::class)->utcFormat('2023-01-01 09:00:00'),
        'ended_at'   => app(DateTimeFormatter::class)->utcFormat('2023-01-01 17:00:00'),
    ]);

    $this->actingAsUser();

    $this->postJson(route('session.pending-tasks.store', $session), ['task_id' => $extraTask->id])
        ->assertSuccessful();

    $this->assertDatabaseHas('session_pending_tasks', ['session_id' => $session->id, 'task_id' => $extraTask->id]);

    $this->deleteJson(route('session.pending-tasks.destroy', [$session, $extraTask]))
        ->assertSuccessful();

    $this->assertDatabaseMissing('session_pending_tasks', ['session_id' => $session->id, 'task_id' => $extraTask->id]);
});

it('seeds the session task plus the added task on the first link of a finished session', function () {
    $originalTask = Task::factory()->create();
    $extraTask = Task::factory()->create();

    $session = Session::factory()->create([
        'task_id' => $originalTask->id,
    ]);

    $this->actingAsUser();

    $this->postJson(route('session.pending-tasks.store', $session), ['task_id' => $extraTask->id])
        ->assertSuccessful()
        ->assertJsonCount(2, 'pending_tasks');

    expect($session->pendingTasks()->pluck('task_id')->sort()->values()->all())
        ->toBe(collect([$originalTask->id, $extraTask->id])->sort()->values()->all());

    expect($session->fresh()->pending_tasks_seeded_at)->not->toBeNull();
});

it('does not resurrect a removed seeded task when the set is emptied and refilled', function () {
    $taskA = Task::factory()->create();
    $taskB = Task::factory()->create();
    $taskD = Task::factory()->create();

    $session = Session::factory()->create([
        'task_id' => $taskA->id,
    ]);

    $this->actingAsUser();

    // First add seeds the session's own task A alongside B -> {A, B}.
    $this->postJson(route('session.pending-tasks.store', $session), ['task_id' => $taskB->id])
        ->assertSuccessful();

    expect($session->pendingTasks()->pluck('task_id')->sort()->values()->all())
        ->toBe(collect([$taskA->id, $taskB->id])->sort()->values()->all());

    // Remove both legitimately removable rows -> {}.
    $this->deleteJson(route('session.pending-tasks.destroy', [$session, $taskA]))->assertSuccessful();
    $this->deleteJson(route('session.pending-tasks.destroy', [$session, $taskB]))->assertSuccessful();

    expect($session->pendingTasks()->count())->toBe(0);

    // Adding D must NOT resurrect A: the set is exactly {D}.
    $this->postJson(route('session.pending-tasks.store', $session), ['task_id' => $taskD->id])
        ->assertSuccessful();

    expect($session->pendingTasks()->pluck('task_id')->all())->toBe([$taskD->id]);
    expect($session->fresh()->pending_tasks_seeded_at)->not->toBeNull();
});

it('never seeds or stamps when linking to a session with no task', function () {
    $task = Task::factory()->create();

    $session = Session::factory()->create([
        'task_id' => null,
    ]);

    $this->actingAsUser();

    $this->postJson(route('session.pending-tasks.store', $session), ['task_id' => $task->id])
        ->assertSuccessful()
        ->assertJsonCount(1, 'pending_tasks');

    expect($session->pendingTasks()->pluck('task_id')->all())->toBe([$task->id]);
    expect($session->fresh()->pending_tasks_seeded_at)->toBeNull();
});

it('rejects linking and unlinking on a locked date', function () {
    $this->usingTestDisplayTimeZone();

    $task = Task::factory()->create();
    $extraTask = Task::factory()->create();

    $session = Session::factory()->create([
        'task_id'    => $task->id,
        'started_at' => app(DateTimeFormatter::class)->utcFormat('2023-01-01 09:00:00'),
        'ended_at'   => app(DateTimeFormatter::class)->utcFormat('2023-01-01 17:00:00'),
    ]);

    SessionPendingTask::factory()->create(['session_id' => $session->id, 'task_id' => $task->id]);

    $user = $this->actingAsUser();

    LockedSessionDate::create([
        'user_id' => $user->id,
        'date'    => $session->localStartedAt->format('Y-m-d'),
    ]);

    $this->postJson(route('session.pending-tasks.store', $session), ['task_id' => $extraTask->id])
        ->assertForbidden();

    $this->deleteJson(route('session.pending-tasks.destroy', [$session, $task]))
        ->assertForbidden();

    $this->assertDatabaseHas('session_pending_tasks', ['session_id' => $session->id, 'task_id' => $task->id]);
    $this->assertDatabaseMissing('session_pending_tasks', ['session_id' => $session->id, 'task_id' => $extraTask->id]);
});

it('can retroactively link a finished session and split it into one session per task', function () {
    $originalTask = Task::factory()->create();
    $extraTask = Task::factory()->create();

    $session = Session::factory()->create([
        'task_id'    => $originalTask->id,
        'started_at' => app(DateTimeFormatter::class)->utcFormat('2023-01-01 09:00:00'),
        'ended_at'   => app(DateTimeFormatter::class)->utcFormat('2023-01-01 17:00:00'),
    ]);

    $this->actingAsUser();

    $this->postJson(route('session.pending-tasks.store', $session), ['task_id' => $extraTask->id])
        ->assertSuccessful()
        ->assertJsonCount(2, 'pending_tasks');

    $this->post(route('session.split', $session->id), [
        'from_pending_tasks' => true,
        'segments'           => [
            [
                'started_at' => '2023-01-01 09:00:00',
                'ended_at'   => '2023-01-01 13:00:00',
                'task_id'    => $originalTask->id,
            ],
            [
                'started_at' => '2023-01-01 13:00:00',
                'ended_at'   => '2023-01-01 17:00:00',
                'task_id'    => $extraTask->id,
            ],
        ],
    ])->assertRedirect(route('session.index'))->assertSessionHas('success');

    expect(Session::count())->toBe(2);
    expect(Session::pluck('task_id')->sort()->values()->all())
        ->toBe(collect([$originalTask->id, $extraTask->id])->sort()->values()->all());
    expect(SessionPendingTask::where('session_id', $session->id)->count())->toBe(0);
});

it('flags only the earliest of several running sessions with two or more pending tasks', function () {
    $earliest = Session::factory()->running()->create([
        'started_at' => app(DateTimeFormatter::class)->utcFormat('2023-01-01 09:00:00'),
    ]);
    $latest = Session::factory()->running()->create([
        'started_at' => app(DateTimeFormatter::class)->utcFormat('2023-01-01 11:00:00'),
    ]);

    foreach ([$earliest, $latest] as $session) {
        SessionPendingTask::factory()->create(['session_id' => $session->id, 'task_id' => Task::factory()->create()->id]);
        SessionPendingTask::factory()->create(['session_id' => $session->id, 'task_id' => Task::factory()->create()->id]);
    }

    $this->actingAsUser();

    $this->post(route('session.stop'))
        ->assertRedirect(route('session.index'))
        ->assertSessionHas('splitPendingSessionId', $earliest->id);

    expect($earliest->pendingTasks()->count())->toBe(2);
    expect($latest->pendingTasks()->count())->toBe(2);
});

it('rejects linking a task whose project is archived', function () {
    $session = Session::factory()->running()->create();
    $archivedProject = Project::factory()->create(['archived_at' => now()]);
    $task = Task::factory()->create(['project_id' => $archivedProject->id]);

    $this->actingAsUser();

    $this->postJson(route('session.pending-tasks.store', $session), ['task_id' => $task->id])
        ->assertUnprocessable()
        ->assertJsonValidationErrors(['task_id']);

    $this->assertDatabaseMissing('session_pending_tasks', ['session_id' => $session->id, 'task_id' => $task->id]);
});

it('rejects linking a task whose status is closed', function () {
    $session = Session::factory()->running()->create();
    $closedStatus = TaskStatus::factory()->closed()->create();
    $task = Task::factory()->create([
        'project_id' => $closedStatus->project_id,
        'status_id'  => $closedStatus->id,
    ]);

    $this->actingAsUser();

    $this->postJson(route('session.pending-tasks.store', $session), ['task_id' => $task->id])
        ->assertUnprocessable()
        ->assertJsonValidationErrors(['task_id']);

    $this->assertDatabaseMissing('session_pending_tasks', ['session_id' => $session->id, 'task_id' => $task->id]);
});

it('does not issue a query per session when loading pending tasks on the index', function () {
    $this->usingTestDisplayTimeZone();

    $this->actingAsUser();

    $buildLinkedSessions = function (int $count): void {
        for ($i = 0; $i < $count; $i++) {
            $session = Session::factory()->create([
                'task_id' => Task::factory()->create()->id,
            ]);

            SessionPendingTask::factory()->create(['session_id' => $session->id, 'task_id' => Task::factory()->create()->id]);
            SessionPendingTask::factory()->create(['session_id' => $session->id, 'task_id' => Task::factory()->create()->id]);
        }
    };

    $buildLinkedSessions(2);

    DB::enableQueryLog();
    $this->get(route('session.index', ['per-page' => 100]))->assertSuccessful();
    $queriesWithFewSessions = count(DB::getQueryLog());
    DB::disableQueryLog();
    DB::flushQueryLog();

    $buildLinkedSessions(6);

    DB::enableQueryLog();
    $this->get(route('session.index', ['per-page' => 100]))->assertSuccessful();
    $queriesWithManySessions = count(DB::getQueryLog());
    DB::disableQueryLog();

    // Eager loading keeps the query count flat: tripling the linked-session count
    // must not add per-session queries.
    expect($queriesWithManySessions)->toBeLessThanOrEqual($queriesWithFewSessions);
});

it('clears all pending links on a generic split of a session with linked tasks', function () {
    $task = Task::factory()->create();

    $session = Session::factory()->create([
        'task_id'    => $task->id,
        'started_at' => app(DateTimeFormatter::class)->utcFormat('2023-01-01 09:00:00'),
        'ended_at'   => app(DateTimeFormatter::class)->utcFormat('2023-01-01 17:00:00'),
    ]);

    SessionPendingTask::factory()->create(['session_id' => $session->id, 'task_id' => Task::factory()->create()->id]);
    SessionPendingTask::factory()->create(['session_id' => $session->id, 'task_id' => Task::factory()->create()->id]);

    $this->actingAsUser();

    // Generic "Split Session" flow: no from_pending_tasks flag, segments carry null task_ids.
    $this->post(route('session.split', $session->id), [
        'segments' => [
            [
                'started_at' => '2023-01-01 09:00:00',
                'ended_at'   => '2023-01-01 13:00:00',
                'task_id'    => null,
            ],
            [
                'started_at' => '2023-01-01 13:00:00',
                'ended_at'   => '2023-01-01 17:00:00',
                'task_id'    => null,
            ],
        ],
    ])->assertRedirect(route('session.index'))->assertSessionHas('success');

    expect(Session::count())->toBe(2);
    expect(SessionPendingTask::where('session_id', $session->id)->count())->toBe(0);
});

it('rejects a pending-flow split when a segment has no task assigned', function () {
    $firstTask = Task::factory()->create();
    $secondTask = Task::factory()->create();

    $session = Session::factory()->create([
        'started_at' => app(DateTimeFormatter::class)->utcFormat('2023-01-01 09:00:00'),
        'ended_at'   => app(DateTimeFormatter::class)->utcFormat('2023-01-01 17:00:00'),
    ]);

    SessionPendingTask::factory()->create(['session_id' => $session->id, 'task_id' => $firstTask->id]);
    SessionPendingTask::factory()->create(['session_id' => $session->id, 'task_id' => $secondTask->id]);

    $this->actingAsUser();

    $this->post(route('session.split', $session->id), [
        'from_pending_tasks' => true,
        'segments'           => [
            [
                'started_at' => '2023-01-01 09:00:00',
                'ended_at'   => '2023-01-01 13:00:00',
                'task_id'    => $firstTask->id,
            ],
            [
                'started_at' => '2023-01-01 13:00:00',
                'ended_at'   => '2023-01-01 17:00:00',
                'task_id'    => null,
            ],
        ],
    ])->assertRedirect(route('session.index'))->assertSessionHas('error');

    expect(Session::count())->toBe(1);
    expect($session->pendingTasks()->count())->toBe(2);
});

it('breaks a started_at tie by session id when flagging the split-pending session', function () {
    $sharedStart = app(DateTimeFormatter::class)->utcFormat('2023-01-01 09:00:00');

    $first = Session::factory()->running()->create(['started_at' => $sharedStart]);
    $second = Session::factory()->running()->create(['started_at' => $sharedStart]);

    $earliestById = collect([$first, $second])->sortBy('id')->first();

    foreach ([$first, $second] as $session) {
        SessionPendingTask::factory()->create(['session_id' => $session->id, 'task_id' => Task::factory()->create()->id]);
        SessionPendingTask::factory()->create(['session_id' => $session->id, 'task_id' => Task::factory()->create()->id]);
    }

    $this->actingAsUser();

    $this->post(route('session.stop'))
        ->assertRedirect(route('session.index'))
        ->assertSessionHas('splitPendingSessionId', $earliestById->id);
});
