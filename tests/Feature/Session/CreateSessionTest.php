<?php

use App\Models\Invoice;
use App\Models\Project;
use App\Models\Session;
use App\Models\Sprint;
use App\Models\Task;
use App\Models\TaskStatus;
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

it('loads tasks and sprints with project relationships for filtering', function () {
    $project1 = \App\Models\Project::factory()->create();
    $project2 = \App\Models\Project::factory()->create();

    $task1 = Task::factory()->create(['project_id' => $project1->id]);
    $task2 = Task::factory()->create(['project_id' => $project2->id]);

    $sprint1 = Sprint::factory()->create(['project_id' => $project1->id]);
    $sprint2 = Sprint::factory()->create(['project_id' => $project2->id]);

    $this->actingAsUser();

    $response = $this->get(route('session.create'));

    $response->assertSuccessful();

    // Verify tasks are loaded with project relationship
    $response->assertHasProp('tasks', function ($tasks) use ($task1, $project1) {
        expect($tasks)->toBeArray();
        $task1Data = collect($tasks)->firstWhere('id', $task1->id);
        expect($task1Data)->not->toBeNull();
        expect($task1Data['project'])->not->toBeNull();
        expect($task1Data['project']['id'])->toBe($project1->id);
    });

    // Verify sprints are loaded with project relationship
    $response->assertHasProp('sprints', function ($sprints) use ($sprint1, $project1) {
        expect($sprints)->toBeArray();
        $sprint1Data = collect($sprints)->firstWhere('id', $sprint1->id);
        expect($sprint1Data)->not->toBeNull();
        expect($sprint1Data['project'])->not->toBeNull();
        expect($sprint1Data['project']['id'])->toBe($project1->id);
    });
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

it('excludes completed tasks from the task dropdown when creating a session', function () {
    $project = Project::factory()->create();
    
    $completedStatus = TaskStatus::factory()->completed()->create(['project_id' => $project->id]);
    $incompleteStatus = TaskStatus::factory()->create(['project_id' => $project->id]);
    
    $completedTask = Task::factory()->create([
        'project_id' => $project->id,
        'status_id' => $completedStatus->id,
    ]);
    
    $incompleteTask = Task::factory()->create([
        'project_id' => $project->id,
        'status_id' => $incompleteStatus->id,
    ]);
    
    $taskWithoutStatus = Task::factory()->create([
        'project_id' => $project->id,
        'status_id' => null,
    ]);

    $this->actingAsUser();

    $response = $this->get(route('session.create'));

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
