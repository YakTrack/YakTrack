<?php

namespace Tests\Feature\Project;

use App\Models\Project;
use App\Models\TaskStatus;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ShowProjectWithTaskStatusesTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function project_show_includes_task_statuses_with_task_counts()
    {
        $this->actingAsUser();

        $project = Project::factory()->create(['name' => 'Test Project']);

        $status1 = TaskStatus::factory()->create([
            'name'       => 'To Do',
            'project_id' => $project->id,
            'sort_order' => 1,
        ]);

        $status2 = TaskStatus::factory()->create([
            'name'       => 'Done',
            'project_id' => $project->id,
            'sort_order' => 2,
        ]);

        $response = $this->get(route('project.show', $project));

        $response->assertStatus(200);

        // Verify project data is loaded
        $response->assertHasProp('project');
        $project_data = $response->props('project');

        $this->assertEquals($project->id, $project_data['id']);
        $this->assertEquals('Test Project', $project_data['name']);

        // Verify task statuses are loaded
        $this->assertArrayHasKey('task_statuses', $project_data);
        $taskStatuses = $project_data['task_statuses'];

        $this->assertCount(2, $taskStatuses);

        // Verify statuses are ordered by sort_order
        $this->assertEquals('To Do', $taskStatuses[0]['name']);
        $this->assertEquals('Done', $taskStatuses[1]['name']);

        // Verify task counts are included
        $this->assertArrayHasKey('tasks_count', $taskStatuses[0]);
        $this->assertArrayHasKey('tasks_count', $taskStatuses[1]);
    }

    /** @test */
    public function project_show_works_with_no_task_statuses()
    {
        $this->actingAsUser();

        $project = Project::factory()->create(['name' => 'Empty Project']);

        $response = $this->get(route('project.show', $project));

        $response->assertStatus(200);

        $project_data = $response->props('project');
        $this->assertArrayHasKey('task_statuses', $project_data);
        $this->assertEmpty($project_data['task_statuses']);
    }
}
