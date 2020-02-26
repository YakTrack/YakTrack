<?php

namespace Tests\Task;

use App\Models\Project;
use App\Models\Task;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CreateTaskTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function a_user_can_view_the_page_to_create_a_task()
    {
        $this->actingAsUser();

        $project = factory(Project::class)->create();
        $parentTask = factory(Task::class)->create([
            'parent_id' => $project->id,
        ]);

        $response = $this->get(route('task.create'));

        $response->assertSuccessful();

        $response->assertSee($project->name);
        $response->assertSee($parentTask->name);
    }

    /** @test */
    public function a_user_can_submit_a_post_request_to_create_a_task()
    {
        $this->withoutExceptionHandling();

        $this->actingAsUser();

        $project = factory(Project::class)->create();
        $parentTask = factory(Task::class)->create([
            'parent_id' => $project->id,
        ]);

        $response = $this->post(route('task.store'), [
            'name'        => 'Test Task',
            'description' => 'Test task description.',
            'project_id'  => $project->id,
            'parent_id'   => $parentTask->id,
        ]);

        $response->assertRedirect(route('task.index'));

        $this->assertDatabaseHas('tasks', [
            'name'        => 'Test Task',
            'description' => 'Test task description.',
            'project_id'  => $project->id,
            'parent_id'   => $parentTask->id,
        ]);
    }

    /** @test */
    public function a_user_can_submit_a_post_request_to_create_a_task_with_required_fields_only()
    {
        $this->withoutExceptionHandling();

        $this->actingAsUser();

        $project = factory(Project::class)->create();
        $parentTask = factory(Task::class)->create([
            'parent_id' => $project->id,
        ]);

        $response = $this->post(route('task.store'), [
            'name'        => 'Test Task',
        ]);

        $response->assertRedirect(route('task.index'));

        $this->assertDatabaseHas('tasks', [
            'name'        => 'Test Task',
            'description' => '',
            'project_id'  => null,
            'parent_id'   => null,
        ]);
    }
}
