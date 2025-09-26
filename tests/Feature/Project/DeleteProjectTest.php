<?php

namespace Tests\Feature\Project;

use App\Models\Project;
use App\Models\Sprint;
use App\Models\Task;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DeleteProjectTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function a_user_can_delete_a_project()
    {
        $project = Project::factory()->create(['name' => 'Test Project']);

        $this->actingAsUser();

        $response = $this->delete(route('project.destroy', [
            'project' => $project,
        ]));

        $response->assertRedirect(route('project.index'));

        $this->assertDatabaseMissing('projects', [
            'id' => $project->id,
        ]);
    }

    /** @test */
    public function a_user_cannot_delete_a_project_with_sprints()
    {
        $project = Project::factory()->create(['name' => 'Test Project']);
        $sprint = Sprint::factory()->create(['project_id' => $project->id]);

        $this->actingAsUser();

        $response = $this->delete(route('project.destroy', [
            'project' => $project,
        ]));

        $response->assertStatus(422);
    }

    /** @test */
    public function a_user_cannot_delete_a_project_with_tasks()
    {
        $project = Project::factory()->create(['name' => 'Test Project']);
        $task = Task::factory()->create(['project_id' => $project->id]);

        $this->actingAsUser();

        $response = $this->delete(route('project.destroy', [
            'project' => $project,
        ]));

        $response->assertStatus(422);
    }
}
