<?php

namespace Tests\Feature\Task\Session;

use App\Models\Invoice;
use App\Models\Project;
use App\Models\Session;
use App\Models\Sprint;
use App\Models\Task;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CreateSessionTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function a_user_can_view_the_page_to_create_a_session()
    {
        $invoice = factory(Invoice::class)->create();
        $sprint = factory(Sprint::class)->create();
        $task = factory(Task::class)->create();

        $this->withoutExceptionHandling();

        $this->actingAsUser();

        $response = $this->get(route('session.create'));

        $response->assertSuccessful();

        $response->assertSee($invoice->number);
        $response->assertSee($sprint->name);
        $response->assertSee($task->name);
    }

    /** @test */
    public function a_user_can_create_a_session_for_a_task_with_a_post_request()
    {
        $this->withoutExceptionHandling();
        $this->usingTestDisplayTimeZone();

        $this->actingAsUser();

        $task = factory(Task::class)->create();

        $response = $this->post(route('task.session.store', [
            'task'       => $task,
        ]), [
            'started_at' => '2019-01-01 00:00:00',
            'ended_at'   => null,
        ]);

        $response->assertRedirect(route('session.index'));

        $session = Session::orderBy('id', 'desc')->first();

        $this->assertDatabaseHas('sessions', [
            'id'         => $session->id,
            'task_id'    => $task->id,
            'started_at' => '2018-12-31 13:00:00',
            'ended_at'   => null,
        ]);
    }

    /** @test */
    public function a_user_can_create_a_session_for_a_task_and_its_latest_open_sprint_with_a_post_request()
    {
        $this->withoutExceptionHandling();
        $this->usingTestDisplayTimeZone();

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

        $response->assertRedirect(route('session.index'));

        $this->assertDatabaseHas('sessions', [
            'task_id'    => $task->id,
            'started_at' => '2018-12-31 13:00:00',
            'ended_at'   => null,
            'sprint_id'  => $sprint->id,
        ]);
    }
}
