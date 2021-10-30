<?php

namespace Tests\Feature\Task\Session;

use App\Models\Project;
use App\Models\Session;
use App\Models\SessionCategory;
use App\Models\Sprint;
use App\Models\Task;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Tests\TestCase;

class ContinueSessionTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function a_user_can_create_a_new_session_with_the_same_details_as_an_existing_session()
    {
        $this->withoutExceptionHandling();
        $this->usingTestDisplayTimeZone();
        Carbon::setTestNow($now = '2021-01-01 00:00:00');

        $this->actingAsUser();

        $project = factory(Project::class)->create();
        $previousSprint = factory(Sprint::class)->create([
            'project_id' => $project->id,
            'is_open'    => 0,
        ]);
        $currentSprint = factory(Sprint::class)->create([
            'project_id' => $project->id,
            'is_open'    => 1,
        ]);
        $sessionCategory = factory(SessionCategory::class)->create();

        $task = factory(Task::class)->create([
            'project_id' => $project->id,
        ]);

        $existingSession = factory(Session::class)->create([
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
    }
}
