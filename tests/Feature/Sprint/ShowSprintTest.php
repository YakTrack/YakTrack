<?php

namespace Tests\Feature\Sprint;

use App\Models\Project;
use App\Models\Session;
use App\Models\Sprint;
use App\Models\Task;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ShowSprintTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function a_user_can_view_a_single_sprint()
    {
        $this->withoutExceptionHandling();

        $project = factory(Project::class)->create();
        $sprint = factory(Sprint::class)->create(['project_id' => $project->id]);
        $task = factory(Task::class)->create([
            'project_id' => $project->id,
        ]);
        $session1 = factory(Session::class)->create([
            'sprint_id'  => $sprint->id,
            'task_id'    => $task->id,
            'started_at' => '2019-01-01 00:00:00',
            'ended_at'   => '2019-01-01 00:01:00',
        ]);
        $session2 = factory(Session::class)->create([
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
    }
}
