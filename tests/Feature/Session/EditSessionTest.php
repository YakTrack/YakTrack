<?php

use App\Models\Invoice;
use App\Models\Project;
use App\Models\Session;
use App\Models\SessionCategory;
use App\Models\Sprint;
use App\Models\Task;
use App\Models\TaskStatus;
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

it('excludes completed tasks from the task dropdown when editing a session', function () {
    $project = Project::factory()->create();

    $completedStatus = TaskStatus::factory()->completed()->create(['project_id' => $project->id]);
    $incompleteStatus = TaskStatus::factory()->create(['project_id' => $project->id]);

    $completedTask = Task::factory()->create([
        'project_id' => $project->id,
        'status_id'  => $completedStatus->id,
    ]);

    $incompleteTask = Task::factory()->create([
        'project_id' => $project->id,
        'status_id'  => $incompleteStatus->id,
    ]);

    $taskWithoutStatus = Task::factory()->create([
        'project_id' => $project->id,
        'status_id'  => null,
    ]);

    $session = Session::factory()->create([
        'task_id' => $incompleteTask->id,
    ]);

    $this->actingAsUser();

    $response = $this->get(route('session.edit', ['session' => $session]));

    $response->assertSuccessful();

    $response->assertHasProp('tasks', function ($tasks) use ($completedTask, $incompleteTask, $taskWithoutStatus) {
        expect($tasks)->toBeArray();

        $taskIds = collect($tasks)->pluck('id')->toArray();

        // Completed task should NOT be in the list
        expect($taskIds)->not->toContain($completedTask->id);

        // Incomplete task should be in the list
        expect($taskIds)->toContain($incompleteTask->id);

        // Task without status should be in the list
        expect($taskIds)->toContain($taskWithoutStatus->id);
    });
});

it('includes the currently selected completed task when editing a session', function () {
    $project = Project::factory()->create();

    $completedStatus = TaskStatus::factory()->completed()->create(['project_id' => $project->id]);

    $completedTask = Task::factory()->create([
        'project_id' => $project->id,
        'status_id'  => $completedStatus->id,
    ]);

    $session = Session::factory()->create([
        'task_id' => $completedTask->id,
    ]);

    $this->actingAsUser();

    $response = $this->get(route('session.edit', ['session' => $session]));

    $response->assertSuccessful();

    $response->assertHasProp('tasks', function ($tasks) use ($completedTask) {
        expect($tasks)->toBeArray();

        $taskIds = collect($tasks)->pluck('id')->toArray();

        // The currently selected completed task should be included
        expect($taskIds)->toContain($completedTask->id);
    });
});

it('excludes tasks from archived projects from the task dropdown when editing a session', function () {
    $activeProject = Project::factory()->create();
    $archivedProject = Project::factory()->create(['archived_at' => now()]);

    $taskFromActiveProject = Task::factory()->create([
        'project_id' => $activeProject->id,
    ]);

    $taskFromArchivedProject = Task::factory()->create([
        'project_id' => $archivedProject->id,
    ]);

    $session = Session::factory()->create([
        'task_id' => $taskFromActiveProject->id,
    ]);

    $this->actingAsUser();

    $response = $this->get(route('session.edit', ['session' => $session]));

    $response->assertSuccessful();

    $response->assertHasProp('tasks', function ($tasks) use ($taskFromActiveProject, $taskFromArchivedProject) {
        expect($tasks)->toBeArray();

        $taskIds = collect($tasks)->pluck('id')->toArray();

        // Task from active project should be in the list
        expect($taskIds)->toContain($taskFromActiveProject->id);

        // Task from archived project should NOT be in the list
        expect($taskIds)->not->toContain($taskFromArchivedProject->id);
    });
});

it('includes the currently selected task from an archived project when editing a session', function () {
    $archivedProject = Project::factory()->create(['archived_at' => now()]);

    $taskFromArchivedProject = Task::factory()->create([
        'project_id' => $archivedProject->id,
    ]);

    $session = Session::factory()->create([
        'task_id' => $taskFromArchivedProject->id,
    ]);

    $this->actingAsUser();

    $response = $this->get(route('session.edit', ['session' => $session]));

    $response->assertSuccessful();

    $response->assertHasProp('tasks', function ($tasks) use ($taskFromArchivedProject) {
        expect($tasks)->toBeArray();

        $taskIds = collect($tasks)->pluck('id')->toArray();

        // The currently selected task from archived project should be included
        expect($taskIds)->toContain($taskFromArchivedProject->id);
    });
});
