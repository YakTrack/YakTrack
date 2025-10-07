<?php

use App\Models\Invoice;
use App\Models\Session;
use App\Models\SessionCategory;
use App\Models\Sprint;
use App\Models\Task;
use Carbon\Carbon;

it('can load the page to edit a session', function () {
    $this->withoutExceptionHandling();

    $session = Session::factory()->create();

    $this->actingAsUser();

    $response = $this->get(route('session.edit', ['session' => $session]));

    $response->assertHasProp('session', $session->fresh()->toArray());
});

it('can load the page to edit a session in progress', function () {
    $this->withoutExceptionHandling();

    $session = Session::factory()->create([
        'ended_at' => null,
    ]);

    $this->actingAsUser();

    $response = $this->get(route('session.edit', ['session' => $session]));

    $response->assertHasProp('session', $session->fresh()->toArray());
});

it('can send a patch request to edit a session', function () {
    // Default test display timezone is Asutralia/Sydney which corresponds to UTC+11 during
    // daylight savings in January
    $this->usingTestDisplayTimeZone();

    $this->withoutExceptionHandling();

    $session = Session::factory()->create([
        'started_at' => '2018-01-01 00:00:00',
        'ended_at'   => '2018-01-01 01:00:00',
    ]);

    $newTask = Task::factory()->create();
    $newInvoice = Invoice::factory()->create();
    $newSprint = Sprint::factory()->create();
    $sessionCategory = SessionCategory::factory()->create();

    $this->actingAsUser();

    $response = $this->patch(route('session.update', ['session' => $session]), [
        // Started at and ended at times ae submitted in display timezone
        'started_at'            => '2018-01-01 12:00:00',
        // Started at and ended at times are submitted in display timezone
        'ended_at'              => '2018-01-01 13:00:00',
        'task_id'               => $newTask->id,
        'invoice_id'            => $newInvoice->id,
        'sprint_id'             => $newSprint->id,
        'session_category_id'   => $sessionCategory->id,
        'comment'               => $comment = str_random(10),
    ]);

    $response->assertRedirect(route('session.index'));

    $this->assertDatabaseHas('sessions', [
        'id'                    => $session->id,
        // Australia/Sydney offset is UTC+11 in January, meaning that the corresponding UTC time for 12:00pm in display timezone
        // will be 1:00am on the same date
        'started_at'            => '2018-01-01 01:00:00',
        // Australia/Sydney offset is UTC+11 in January, meaning that the corresponding UTC time for 1:00pm in display timezone
        // will be 2:00am on the same date
        'ended_at'              => '2018-01-01 02:00:00',
        'task_id'               => $newTask->id,
        'invoice_id'            => $newInvoice->id,
        'sprint_id'             => $newSprint->id,
        'session_category_id'   => $sessionCategory->id,
        'comment'               => $comment,
    ]);
});

it('can send a patch request to edit a session for a session with no task or invoice', function () {
    $this->usingTestDisplayTimezone('UTC');
    $this->withoutExceptionHandling();

    $session = Session::factory()->create([
        'started_at' => '2018-01-01 00:00:00',
        'ended_at'   => null,
    ]);

    $newTask = Task::factory()->create();

    $this->actingAsUser();

    $response = $this->patch(route('session.update', ['session' => $session]), [
        'task_id'    => '',
        'started_at' => '2018-01-01 00:00:00',
        'ended_at'   => '',
    ]);

    $response->assertRedirect(route('session.index'));

    $this->assertDatabaseHas('sessions', [
        'id'            => $session->id,
        'task_id'       => null,
        'invoice_id'    => null,
        'started_at'    => '2018-01-01 00:00:00',
        'ended_at'      => null,
    ]);
});

it('can edit a session with a json patch request', function () {
    $this->usingTestDisplayTimeZone('UTC');
    $this->withoutExceptionHandling();

    $session = Session::factory()->create([
        'started_at' => '2018-01-01 00:00:00',
        'ended_at'   => null,
    ]);

    $this->actingAsUser();

    $response = $this->json('patch', route('session.update', ['session' => $session]), [
        'started_at' => '2018-01-01 00:00:00',
        'ended_at'   => '2018-01-01 12:34:56',
    ]);

    $response->assertRedirect(route('session.index'));

    $this->assertDatabaseHas('sessions', [
        'id'         => $session->id,
        'started_at' => '2018-01-01 00:00:00',
        'ended_at'   => '2018-01-01 12:34:56',
    ]);

    Carbon::setTestNow();
});