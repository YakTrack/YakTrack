<?php

namespace Tests\Feature\Task;

use App\Models\Project;
use App\Models\Task;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class EditTaskTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function a_user_can_load_the_page_to_edit_a_task()
    {
        $task = factory(Task::class)->create();
        $newParentTask = factory(Task::class)->create();
        $newProject = factory(Project::class)->create();

        $this->actingAsUser();

        $response = $this->get(route('task.edit', ['task' => $task]));

        $response->assertSuccessful();

        $response->assertSee($newProject->name);
        $response->assertSee($newParentTask->name);
    }

    /** @test */
    public function a_user_can_update_a_task_with_a_patch_request()
    {
        $this->withoutExceptionHandling();

        $task = factory(Task::class)->create();
        $newParentTask = factory(Task::class)->create();
        $newProject = factory(Project::class)->create();

        $this->actingAsUser();

        $response = $this->patch(route('task.update', ['task' => $task]), $updatedTaskDetails = [
            'name'        => 'Updated Task Name',
            'description' => 'Updated task description.',
            'parent_id'   => $newParentTask->id,
            'project_id'  => $newProject->id,
        ]);

        $response->assertRedirect(route('task.index'));

        $this->assertDatabaseHas('tasks', $updatedTaskDetails);
    }
}
