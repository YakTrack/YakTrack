<?php

namespace Tests\Feature\Task\Session;

use App\Models\Project;
use App\Models\Session;
use App\Models\Sprint;
use App\Models\Task;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class CreateSessionTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function a_user_can_create_a_session_for_a_task_with_a_post_request()
    {
        $this->withoutExceptionHandling();

        $this->actingAsUser();

        $task = factory(Task::class)->create();

        $response = $this->post(route('task.session.store', [
            'task'       => $task,
        ]), [
            'started_at' => '2019-01-01 00:00:00',
            'ended_at'   => null,
        ]);

        $response->assertSuccessful();

        $session = Session::orderBy('id', 'desc')->first();

        $response->assertJson([
            'data' => [
                'id'         => $session->id,
                'task_id'    => $task->id,
                'started_at' => json_decode($session->toJson(), true)['started_at'],
                'ended_at'   => null,
            ]
        ]);
    }

    /** @test */
    public function a_user_can_create_a_session_for_a_task_and_its_latest_open_sprint_with_a_post_request()
    {
        $this->withoutExceptionHandling();

        $this->actingAsUser();

        $project = factory(Project::class)->create();

        $sprint = factory(Sprint::class)->create([
            'project_id' => $project,
            'is_open'    => true,
        ]);

        $task = factory(Task::class)->create([
            'project_id' => $project->id,
        ]);

        $response = $this->post(route('task.session.store', [
            'task'       => $task,
        ]), [
            'started_at' => '2019-01-01 00:00:00',
            'ended_at'   => null,
        ]);

        $response->assertSuccessful();

        $session = Session::orderBy('id', 'desc')->first();

        $response->assertJson([
            'data' => [
                'id'         => $session->id,
                'task_id'    => $task->id,
                'started_at' => json_decode($session->toJson(), true)['started_at'],
                'ended_at'   => null,
                'sprint_id'  => $sprint->id,
            ]
        ]);
    }
}
