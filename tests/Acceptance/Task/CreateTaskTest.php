<?php

namespace Tests\Task;

use App\Models\Project;
use App\Models\Sprint;
use App\Models\Task;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Tests\TestCase;

class CreateTaskTest extends TestCase
{
    use DatabaseMigrations;

    /** @test */
    public function a_logged_in_user_can_create_a_task()
    {
        $this->withoutExceptionHandling();

        $this->actingAsUser();

        // Create project
        $project = factory(Project::class)->create();

        // Create sprint
        $sprint = factory(Sprint::class)->create(['project_id' => $project->id]);

        // Create parent task
        $parentTask = factory(Task::class)->create([
            'parent_id' => $project->id,
        ]);

        // Visit page
        $response = $this->get(route('task.create'));

        $response->assertSuccessful();

        // Verify correct page loads
        $response->assertViewIs('task.create');

        // Simulate form submission
        $response = $this->post(route('task.store'), [
            'name'        => 'Test Task',
            'description' => 'Test task description.',
            'project_id'  => $project->id,
            'parent_id'   => $parentTask->id,
        ]);

        // Verify redirected to correct page
        $response->assertRedirect(route('task.index'));

        // Verify task added to database
        $this->assertDatabaseHas('tasks', [
            'name'        => 'Test Task',
            'description' => 'Test task description.',
            'project_id'  => $project->id,
            'parent_id'   => $parentTask->id,
        ]);
    }
}
