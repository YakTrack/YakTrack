<?php

use App\Models\Project;
use App\Models\Session;
use App\Models\SessionCategory;
use App\Models\Sprint;
use App\Models\Task;
use Illuminate\Support\Carbon;

it('can create a new session with the same details as an existing session', function () {
    $this->withoutExceptionHandling();
    $this->usingTestDisplayTimeZone();
    Carbon::setTestNow($now = '2021-01-01 00:00:00');

    $this->actingAsUser();

    $project = Project::factory()->create();
    $previousSprint = Sprint::factory()->create([
        'project_id' => $project->id,
        'is_open'    => 0,
    ]);
    $currentSprint = Sprint::factory()->create([
        'project_id' => $project->id,
        'is_open'    => 1,
    ]);
    $sessionCategory = SessionCategory::factory()->create();

    $task = Task::factory()->create([
        'project_id' => $project->id,
    ]);

    $existingSession = Session::factory()->create([
        'task_id'               => $task->id,
        'sprint_id'             => $previousSprint->id,
        'session_category_id'   => $sessionCategory->id,
        'is_billable'           => 1,
    ]);

    $response = $this->post(route('session.continue', [
        'session' => $existingSession,
    ]));

    $response->assertRedirect(route('session.index'));

    $session = Session::orderBy('id', 'desc')->first();

    $this->assertDatabaseHas('sessions', [
        'id'                    => $session->id,
        'task_id'               => $existingSession->task->id,
        'is_billable'           => 1,
        'started_at'            => $now,
        'ended_at'              => null,
        'sprint_id'             => $currentSprint->id,
        'session_category_id'   => $sessionCategory->id,
    ]);
});

it('can create a new session with the same details as an existing session when there is no open sprint', function () {
    $this->withoutExceptionHandling();
    $this->usingTestDisplayTimeZone();
    Carbon::setTestNow($now = '2021-01-01 00:00:00');

    $this->actingAsUser();

    $project = Project::factory()->create();
    $previousSprint = Sprint::factory()->create([
        'project_id' => $project->id,
        'is_open'    => 0,
    ]);
    $sessionCategory = SessionCategory::factory()->create();

    $task = Task::factory()->create([
        'project_id' => $project->id,
    ]);

    $existingSession = Session::factory()->create([
        'task_id'               => $task->id,
        'sprint_id'             => $previousSprint->id,
        'session_category_id'   => $sessionCategory->id,
        'is_billable'           => 1,
    ]);

    $response = $this->post(route('session.continue', [
        'session' => $existingSession,
    ]));

    $response->assertRedirect(route('session.index'));

    $session = Session::orderBy('id', 'desc')->first();

    $this->assertDatabaseHas('sessions', [
        'id'                    => $session->id,
        'task_id'               => $existingSession->task->id,
        'is_billable'           => 1,
        'started_at'            => $now,
        'ended_at'              => null,
        'sprint_id'             => $previousSprint->id,
        'session_category_id'   => $sessionCategory->id,
    ]);
});
