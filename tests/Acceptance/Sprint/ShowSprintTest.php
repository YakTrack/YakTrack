<?php

use App\Models\Project;
use App\Models\Session;
use App\Models\Sprint;
use App\Models\Task;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Tests\TestCase;

class ShowSprintTest extends TestCase
{
    use DatabaseMigrations;

    /** @test */
    public function a_logged_in_user_can_view_details_of_a_sprint()
    {
        $this->withoutExceptionHandling();

        // Create project and sprint
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

        // Login user
        $user = $this->actingAsUser();

        // Visit show sprint page
        $response = $this->get(route('sprint.show', ['sprint' => $sprint]));

        $response->assertSuccessful();

        // Verify correct page has loaded
        $response->assertViewIs('sprint.show');

        // Verify sprint data is included in view
        $response->assertSee($sprint->name);
        $response->assertSee($project->name);
        $response->assertSee('01:23');
    }
}
