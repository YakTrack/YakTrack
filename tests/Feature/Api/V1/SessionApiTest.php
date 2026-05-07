<?php

use App\Models\Session;
use App\Models\Task;
use App\Models\User;
use Carbon\Carbon;
use Laravel\Sanctum\Sanctum;

beforeEach(function () {
    Sanctum::actingAs(User::factory()->create());
});

it('returns 401 when unauthenticated', function () {
    app('auth')->forgetGuards();

    $this->getJson('/api/v1/sessions')
        ->assertUnauthorized();
});

it('can list sessions', function () {
    Session::factory()->count(3)->create();

    $this->getJson('/api/v1/sessions')
        ->assertSuccessful()
        ->assertJsonCount(3, 'data')
        ->assertJsonStructure([
            'data' => [['id', 'started_at', 'ended_at', 'is_running', 'duration_in_seconds', 'created_at']],
        ]);
});

it('can create a session', function () {
    $task = Task::factory()->create();

    $this->postJson('/api/v1/sessions', [
        'started_at' => '2026-01-15 09:00:00',
        'ended_at'   => '2026-01-15 10:00:00',
        'task_id'    => $task->id,
        'comment'    => 'API session',
    ])
        ->assertCreated()
        ->assertJsonPath('data.comment', 'API session')
        ->assertJsonPath('data.task_id', $task->id);
});

it('stops running sessions when creating a session without ended_at', function () {
    $running = Session::factory()->running()->create();

    $this->postJson('/api/v1/sessions', [
        'started_at' => '2026-01-15 09:00:00',
    ])->assertCreated();

    expect($running->fresh()->ended_at)->not->toBeNull();
});

it('validates required fields when creating a session', function () {
    $this->postJson('/api/v1/sessions', [])
        ->assertUnprocessable()
        ->assertJsonValidationErrors(['started_at']);
});

it('validates ended_at is after started_at', function () {
    $this->postJson('/api/v1/sessions', [
        'started_at' => '2026-01-15 10:00:00',
        'ended_at'   => '2026-01-15 09:00:00',
    ])->assertUnprocessable()
        ->assertJsonValidationErrors(['ended_at']);
});

it('can show a session', function () {
    $session = Session::factory()->create();

    $this->getJson("/api/v1/sessions/{$session->id}")
        ->assertSuccessful()
        ->assertJsonPath('data.id', $session->id);
});

it('can update a session', function () {
    $session = Session::factory()->create();

    $this->putJson("/api/v1/sessions/{$session->id}", [
        'comment' => 'Updated comment',
    ])
        ->assertSuccessful()
        ->assertJsonPath('data.comment', 'Updated comment');
});

it('can delete a session', function () {
    $session = Session::factory()->create();

    $this->deleteJson("/api/v1/sessions/{$session->id}")
        ->assertNoContent();

    $this->assertDatabaseMissing('sessions', ['id' => $session->id]);
});

it('can start a new session', function () {
    Carbon::setTestNow('2026-01-15 09:00:00');

    $this->postJson('/api/v1/sessions/start')
        ->assertCreated()
        ->assertJsonPath('data.is_running', true)
        ->assertJsonPath('data.is_billable', true);

    $this->assertDatabaseHas('sessions', [
        'ended_at'    => null,
        'is_billable' => true,
    ]);
});

it('stops running sessions when starting a new one', function () {
    $running = Session::factory()->running()->create();

    $this->postJson('/api/v1/sessions/start')
        ->assertCreated();

    expect($running->fresh()->ended_at)->not->toBeNull();
});

it('can stop running sessions', function () {
    $running1 = Session::factory()->running()->create();
    $running2 = Session::factory()->running()->create();

    $response = $this->postJson('/api/v1/sessions/stop')
        ->assertSuccessful();

    expect($response->json('message'))->toBe('2 session(s) stopped.');
    expect($running1->fresh()->ended_at)->not->toBeNull();
    expect($running2->fresh()->ended_at)->not->toBeNull();
});

it('can continue a session', function () {
    $task = Task::factory()->create();
    $session = Session::factory()->create([
        'task_id'     => $task->id,
        'is_billable' => true,
    ]);

    $this->postJson("/api/v1/sessions/{$session->id}/continue")
        ->assertCreated()
        ->assertJsonPath('data.task_id', $task->id)
        ->assertJsonPath('data.is_running', true);
});

it('stops running sessions when continuing a session', function () {
    $running = Session::factory()->running()->create();
    $session = Session::factory()->create();

    $this->postJson("/api/v1/sessions/{$session->id}/continue")
        ->assertCreated();

    expect($running->fresh()->ended_at)->not->toBeNull();
});
