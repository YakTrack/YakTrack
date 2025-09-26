<?php

namespace Tests\Feature\TaskStatus;

use App\Models\Project;
use App\Models\TaskStatus;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class IndexTaskStatusTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function a_user_can_list_task_statuses_for_a_specific_project()
    {
        $this->withoutExceptionHandling();
        $this->actingAsUser();

        $project1 = Project::factory()->create(['name' => 'Project One']);
        $project2 = Project::factory()->create(['name' => 'Project Two']);

        $status1 = TaskStatus::factory()->create([
            'name'       => 'To Do',
            'project_id' => $project1->id,
            'sort_order' => 1,
        ]);

        $status2 = TaskStatus::factory()->create([
            'name'       => 'In Progress',
            'project_id' => $project1->id,
            'sort_order' => 2,
        ]);

        // Status for different project
        $status3 = TaskStatus::factory()->create([
            'name'       => 'Done',
            'project_id' => $project2->id,
            'sort_order' => 1,
        ]);

        $response = $this->get(route('task-status.index', ['project_id' => $project1->id]));

        $response->assertStatus(200);

        $response->assertPropCount('taskStatuses', 2); // Only 2 statuses for project1

        $taskStatuses = $response->props('taskStatuses');

        $this->assertArrayMatches([
            [
                'id'         => $status1->id,
                'name'       => 'To Do',
                'project_id' => $project1->id,
                'sort_order' => 1,
            ],
            [
                'id'         => $status2->id,
                'name'       => 'In Progress',
                'project_id' => $project1->id,
                'sort_order' => 2,
            ],
        ], $taskStatuses);

        // Ensure the other project's status is not included
        $foundStatus3 = collect($taskStatuses)->contains('id', $status3->id);
        $this->assertFalse($foundStatus3, 'Status from different project should not be included');
    }

    /** @test */
    public function task_statuses_are_returned_ordered_by_sort_order()
    {
        $this->actingAsUser();

        $project = Project::factory()->create();

        TaskStatus::factory()->create([
            'name'       => 'Last',
            'project_id' => $project->id,
            'sort_order' => 3,
        ]);

        TaskStatus::factory()->create([
            'name'       => 'First',
            'project_id' => $project->id,
            'sort_order' => 1,
        ]);

        TaskStatus::factory()->create([
            'name'       => 'Middle',
            'project_id' => $project->id,
            'sort_order' => 2,
        ]);

        $response = $this->get(route('task-status.index', ['project_id' => $project->id]));

        $response->assertStatus(200);

        $taskStatuses = $response->props('taskStatuses');

        $this->assertEquals('First', $taskStatuses[0]['name']);
        $this->assertEquals('Middle', $taskStatuses[1]['name']);
        $this->assertEquals('Last', $taskStatuses[2]['name']);
    }

    /** @test */
    public function a_user_can_list_all_task_statuses_when_no_project_id_is_provided()
    {
        $this->actingAsUser();

        $project1 = Project::factory()->create(['name' => 'Project One']);
        $project2 = Project::factory()->create(['name' => 'Project Two']);

        TaskStatus::factory()->create([
            'name'       => 'Status 1',
            'project_id' => $project1->id,
        ]);

        TaskStatus::factory()->create([
            'name'       => 'Status 2',
            'project_id' => $project2->id,
        ]);

        $response = $this->get(route('task-status.index'));

        $response->assertStatus(200);

        $response->assertPropCount('taskStatuses', 2); // Both statuses should be returned

        $taskStatuses = $response->props('taskStatuses');

        // Should include project relationship
        $this->assertArrayHasKey('id', $taskStatuses[0]);
        $this->assertArrayHasKey('name', $taskStatuses[0]);
        $this->assertArrayHasKey('color', $taskStatuses[0]);
        $this->assertArrayHasKey('sort_order', $taskStatuses[0]);
        $this->assertArrayHasKey('is_default', $taskStatuses[0]);
        $this->assertArrayHasKey('is_completed', $taskStatuses[0]);
        $this->assertArrayHasKey('project_id', $taskStatuses[0]);
        $this->assertArrayHasKey('created_at', $taskStatuses[0]);
        $this->assertArrayHasKey('updated_at', $taskStatuses[0]);
        $this->assertArrayHasKey('project', $taskStatuses[0]);
        $this->assertArrayHasKey('id', $taskStatuses[0]['project']);
        $this->assertArrayHasKey('name', $taskStatuses[0]['project']);
    }

    /** @test */
    public function unauthenticated_users_cannot_list_task_statuses()
    {
        $project = Project::factory()->create();

        TaskStatus::factory()->create([
            'project_id' => $project->id,
        ]);

        $response = $this->get(route('task-status.index'));

        $response->assertStatus(302); // Redirect to login
        $response->assertRedirect(route('login'));
    }

    /** @test */
    public function empty_result_is_returned_for_project_with_no_statuses()
    {
        $this->actingAsUser();

        $project = Project::factory()->create();

        $response = $this->get(route('task-status.index', ['project_id' => $project->id]));

        $response->assertStatus(200);
        $response->assertPropCount('taskStatuses', 0);

        $taskStatuses = $response->props('taskStatuses');
        $this->assertEquals([], $taskStatuses);
    }

    /** @test */
    public function nonexistent_project_id_returns_404()
    {
        $this->actingAsUser();

        $response = $this->get(route('task-status.index', ['project_id' => 99999]));

        $response->assertStatus(404);
    }
}
