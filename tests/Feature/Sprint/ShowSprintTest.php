<?php

use App\Models\Project;
use App\Models\Session;
use App\Models\Sprint;
use App\Models\Task;
use Inertia\Testing\AssertableInertia as Assert;

it('can view a single sprint', function () {
    $this->withoutExceptionHandling();

    $project = Project::factory()->create();
    $sprint = Sprint::factory()->create(['project_id' => $project->id]);
    $otherSprint = Sprint::factory()->create(['project_id' => $project->id]);
    $task = Task::factory()->create([
        'project_id' => $project->id,
    ]);

    // Sessions in sprint
    $session1 = Session::factory()->create([
        'sprint_id'  => $sprint->id,
        'task_id'    => $task->id,
        'started_at' => '2019-01-01 00:00:01',
        'ended_at'   => '2019-01-01 00:01:01',
    ]);
    $session2 = Session::factory()->create([
        'sprint_id'  => $sprint->id,
        'task_id'    => $task->id,
        'started_at' => '2019-01-01 00:01:01',
        'ended_at'   => '2019-01-01 00:01:24',
    ]);

    // Session in other sprint
    Session::factory()->create([
        'sprint_id'  => $otherSprint->id,
        'task_id'    => $task->id,
        'started_at' => '2019-01-01 00:01:01',
        'ended_at'   => '2019-01-01 00:01:24',
    ]);

    $this->actingAsUser();

    $response = $this->get(route('sprint.show', ['sprint' => $sprint]));

    $response->assertInertia(
        fn (Assert $page) => $page
            ->component('Sprint/Show')
            ->has(
                'sprint',
                fn (Assert $page) => $page
                    ->where('id', $sprint->id)
                    ->where('name', $sprint->name)
                    ->where('project_id', $sprint->project_id)
                    ->where('created_at', $sprint->created_at->toIsoString())
                    ->where('updated_at', $sprint->updated_at->toIsoString())
                    ->where('is_open', 0)
                    ->has(
                        'project',
                        fn (Assert $page) => $page
                        ->where('id', $project->id)
                        ->where('name', $project->name)
                        ->where('description', $project->description)
                        ->where('client_id', $project->client_id)
                        ->where('is_billable', 0)
                        ->where('created_at', $project->created_at->toIsoString())
                        ->where('updated_at', $project->updated_at->toIsoString())
                    )->has(
                        'sessions',
                        fn (Assert $page) => $page
                        ->has(
                            0,
                            fn (Assert $page) => $page
                            ->whereAll([
                                'id' => $session1->id,
                            ])
                            ->etc()
                        )->has(
                            1,
                            fn (Assert $page) => $page
                            ->whereAll([
                                'id' => $session2->id,
                            ])
                            ->etc()
                        )
                    )
            )->has(
                'tasks',
                fn (Assert $page) => $page
                    ->has(
                        $task->id,
                        fn (Assert $page) => $page
                        ->where('totalDurationInSprintForHumans', '0:01:23')
                        ->etc()
                    )
            )
    );
});
