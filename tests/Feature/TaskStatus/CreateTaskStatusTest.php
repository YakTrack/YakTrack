<?php

namespace Tests\Feature\TaskStatus;

use App\Models\Project;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CreateTaskStatusTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function a_user_can_visit_create_task_status_page()
    {
        $this->actingAsUser();

        $project = factory(Project::class)->create();

        $response = $this->get(route('task-status.create', ['project_id' => $project->id]));

        $response->assertStatus(200);
        $response->assertHasProp('projects');
        $response->assertHasProp('project');

        $projectData = $response->props('project');
        $this->assertEquals($project->id, $projectData['id']);
    }

    /** @test */
    public function a_user_can_visit_create_task_status_page_without_project_id()
    {
        $this->actingAsUser();

        $response = $this->get(route('task-status.create'));

        $response->assertStatus(200);
        $response->assertHasProp('projects');
        $response->assertPropValue('project', null);
    }
}
