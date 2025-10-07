<?php

use App\Models\Project;
use App\Models\Session;
use App\Models\Sprint;
use App\Models\Task;

it('can view a single sprint', function () {
    $this->withoutExceptionHandling();

    $project = Project::factory()->create();
    $sprint = Sprint::factory()->create(['project_id' => $project->id]);
    $task = Task::factory()->create([
        'project_id' => $project->id,
    ]);
    $session1 = Session::factory()->create([
        'sprint_id'  => $sprint->id,
        'task_id'    => $task->id,
        'started_at' => '2019-01-01 00:00:00',
        'ended_at'   => '2019-01-01 00:01:00',
    ]);
    $session2 = Session::factory()->create([
        'sprint_id'  => $sprint->id,
        'task_id'    => $task->id,
        'started_at' => '2019-01-01 00:01:00',
        'ended_at'   => '2019-01-01 00:01:23',
    ]);

    $this->actingAsUser();

    $response = $this->get(route('sprint.show', ['sprint' => $sprint]));

    $response->assertSuccessful();

    $response->assertSee($sprint->name);
    $response->assertSee($project->name);
    $response->assertSee('01:23');
});
