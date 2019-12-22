<?php

use App\Models\Project;
use App\Models\Sprint;
use App\Models\Task;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class DeleteProjectTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function a_user_can_delete_a_project()
    {
        $project = factory(Project::class)->create(['name' => 'Test Project']);

        $this->actingAsUser();

        $response = $this->delete(route('project.destroy', [
            'project' => $project,
        ]));

        // Verify redirected to correct page
        $response->assertRedirect(route('project.index'));

        // Verify project removed from database
        $this->assertDatabaseMissing('projects', [
            'id' => $project->id,
        ]);
    }

    /** @test */
    public function a_user_cannot_delete_a_project_with_sprints()
    {
        $project = factory(Project::class)->create(['name' => 'Test Project']);
        $sprint = factory(Sprint::class)->create(['project_id' => $project->id]);

        $this->actingAsUser();

        $response = $this->delete(route('project.destroy', [
            'project' => $project,
        ]));

        $response->assertStatus(422);
    }

    /** @test */
    public function a_user_cannot_delete_a_project_with_tasks()
    {
        $project = factory(Project::class)->create(['name' => 'Test Project']);
        $task = factory(Task::class)->create(['project_id' => $project->id]);

        $this->actingAsUser();

        $response = $this->delete(route('project.destroy', [
            'project' => $project,
        ]));

        $response->assertStatus(422);
    }
}
