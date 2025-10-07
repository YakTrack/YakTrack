<?php

use App\Models\Invoice;
use App\Models\Session;
use App\Models\Sprint;
use App\Models\Task;
use App\Support\DateTimeFormatter;

it('can view the page to create a session', function () {
    $invoice = Invoice::factory()->create();
    $sprint = Sprint::factory()->create();
    $task = Task::factory()->create();

    $this->withoutExceptionHandling();

    $this->actingAsUser();

    $response = $this->get(route('session.create'));

    $response->assertSuccessful();

    $response->assertSee($invoice->number);
    $response->assertSee($sprint->name);
    $response->assertSee($task->name);
});

it('can create a session with a post request', function () {
    $invoice = Invoice::factory()->create();
    $sprint = Sprint::factory()->create();
    $task = Task::factory()->create();

    $this->withoutExceptionHandling();

    $this->actingAsUser();

    $response = $this->post(route('session.store'), [
        'started_at'    => '2018-01-01 12:34:56',
        'ended_at'      => '2018-01-01 12:34:57',
        'sprint_id'     => $sprint->id,
        'invoice_id'    => $invoice->id,
        'task_id'       => $task->id,
    ]);

    $response->assertRedirect(route('session.index'));

    $this->assertDatabaseHas('sessions', [
        'started_at'    => app(DateTimeFormatter::class)->utcFormat('2018-01-01 12:34:56'),
        'ended_at'      => app(DateTimeFormatter::class)->utcFormat('2018-01-01 12:34:57'),
        'sprint_id'     => $sprint->id,
        'invoice_id'    => $invoice->id,
        'task_id'       => $task->id,
    ]);
});

it('can create a session with a post request with the minimum required fields', function () {
    $previouslyRunningSession = Session::factory()->running()->create();

    $this->withoutExceptionHandling();

    $this->actingAsUser();

    $response = $this->post(route('session.store'), [
        'started_at' => '2018-01-01 12:34:56',
    ]);

    $response->assertRedirect(route('session.index'));

    $this->assertDatabaseHas('sessions', [
        'started_at' => app(DateTimeFormatter::class)->utcFormat('2018-01-01 12:34:56'),
        'ended_at'   => null,
    ]);

    expect($previouslyRunningSession->fresh()->isRunning)->toBeFalse();
});
