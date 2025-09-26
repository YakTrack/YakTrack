<?php

namespace Tests\Feature\Project;

use App\Models\Project;
use App\Models\Task;
use App\Models\TaskStatus;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ShowProjectTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function a_logged_in_user_can_view_details_of_a_project()
    {
        $project = Project::factory()->create();

        $this->actingAsUser();

        $response = $this->get(route('project.show', ['project' => $project]));

        $response->assertSuccessful();

        // escape the apostrophe in the same manner as blade is
        $response->assertSee($project->name);

        $response->assertSee($project->description);
        $response->assertSee(e($project->client->name));
    }

    /** @test */
    public function a_logged_in_user_can_view_details_of_a_project_with_an_apostrophe_in_the_name()
    {
        $project = Project::factory()->create(['name' => 'Steve\'s Test Project']);

        $this->actingAsUser();

        $response = $this->get(route('project.show', ['project' => $project]));

        $response->assertSuccessful();

        // escape the apostrophe in the same manner as blade is
        $response->assertSee($project->name);

        $response->assertSee($project->description);
        $response->assertSee($project->client->name);
    }

    /** @test */
    public function project_show_page_includes_paginated_tasks_data()
    {
        $project = Project::factory()->create();
        
        // Create some tasks for the project
        $tasks = Task::factory()->count(3)->create(['project_id' => $project->id]);

        $this->actingAsUser();

        $response = $this->get(route('project.show', $project));

        $response->assertSuccessful();

        // Assert that tasks data is passed to Inertia
        $response->assertHasProp('tasks');
        $response->assertHasProp('tasks.data');
        $response->assertPropCount('tasks.data', 3);

        // Assert pagination structure
        $response->assertHasProp('tasks.current_page');
        $response->assertHasProp('tasks.per_page');
        $response->assertHasProp('tasks.total');
        $response->assertPropValue('tasks.total', 3);
        $response->assertPropValue('tasks.per_page', 15);
    }

    /** @test */
    public function project_show_page_displays_task_names_with_proper_structure()
    {
        $project = Project::factory()->create();
        $taskStatus = TaskStatus::factory()->create(['project_id' => $project->id]);
        $parentTask = Task::factory()->create([
            'project_id' => $project->id,
            'name' => 'Parent Task',
        ]);
        $childTask = Task::factory()->create([
            'project_id' => $project->id,
            'name' => 'Child Task',
            'parent_id' => $parentTask->id,
            'status_id' => $taskStatus->id,
        ]);

        $this->actingAsUser();

        $response = $this->get(route('project.show', $project));

        $response->assertSuccessful();

        // Assert task data structure includes relationships
        $taskData = $response->props()['tasks']['data'];
        
        $childTaskData = collect($taskData)->firstWhere('name', 'Child Task');
        $this->assertNotNull($childTaskData);
        $this->assertEquals('Child Task', $childTaskData['name']);
        $this->assertArrayHasKey('parent', $childTaskData);
        $this->assertArrayHasKey('task_status', $childTaskData);
        $this->assertEquals('Parent Task', $childTaskData['parent']['name']);
        $this->assertEquals($taskStatus->name, $childTaskData['task_status']['name']);
    }

    /** @test */
    public function project_show_page_handles_empty_tasks_list()
    {
        $project = Project::factory()->create();

        $this->actingAsUser();

        $response = $this->get(route('project.show', $project));

        $response->assertSuccessful();
        $response->assertHasProp('tasks');
        $response->assertPropValue('tasks.total', 0);
        $response->assertPropCount('tasks.data', 0);
    }

    /** @test */
    public function project_show_page_paginates_tasks_correctly()
    {
        $project = Project::factory()->create();
        
        // Create more than one page worth of tasks (15 per page)
        $tasks = Task::factory()->count(20)->create(['project_id' => $project->id]);

        $this->actingAsUser();

        // Test first page
        $response = $this->get(route('project.show', $project));
        $response->assertSuccessful();
        $response->assertPropValue('tasks.current_page', 1);
        $response->assertPropValue('tasks.total', 20);
        $response->assertPropCount('tasks.data', 15);

        // Test second page with tasks_page parameter
        $response = $this->get(route('project.show', $project) . '?tasks_page=2');
        $response->assertSuccessful();
        $response->assertPropValue('tasks.current_page', 2);
        $response->assertPropValue('tasks.total', 20);
        $response->assertPropCount('tasks.data', 5);
    }

    /** @test */
    public function project_show_page_orders_tasks_by_name()
    {
        $project = Project::factory()->create();
        
        // Create tasks with specific names to test ordering
        Task::factory()->create(['project_id' => $project->id, 'name' => 'Z Task']);
        Task::factory()->create(['project_id' => $project->id, 'name' => 'A Task']);
        Task::factory()->create(['project_id' => $project->id, 'name' => 'M Task']);

        $this->actingAsUser();

        $response = $this->get(route('project.show', $project));

        $response->assertSuccessful();

        $taskData = $response->props()['tasks']['data'];
        $taskNames = collect($taskData)->pluck('name')->toArray();
        
        $this->assertEquals(['A Task', 'M Task', 'Z Task'], $taskNames);
    }

    /** @test */
    public function project_show_page_only_includes_tasks_for_specific_project()
    {
        $project1 = Project::factory()->create();
        $project2 = Project::factory()->create();
        
        $project1Tasks = Task::factory()->count(2)->create(['project_id' => $project1->id]);
        $project2Tasks = Task::factory()->count(3)->create(['project_id' => $project2->id]);

        $this->actingAsUser();

        $response = $this->get(route('project.show', $project1));

        $response->assertSuccessful();
        $response->assertPropValue('tasks.total', 2);
        
        $taskData = $response->props()['tasks']['data'];
        foreach ($taskData as $task) {
            $this->assertEquals($project1->id, $task['project_id']);
        }
    }

    /** @test */
    public function project_show_page_includes_task_status_with_color()
    {
        $project = Project::factory()->create();
        $taskStatus = TaskStatus::factory()->create([
            'project_id' => $project->id,
            'name' => 'In Progress',
            'color' => '#ff6b6b'
        ]);
        $task = Task::factory()->create([
            'project_id' => $project->id,
            'status_id' => $taskStatus->id
        ]);

        $this->actingAsUser();

        $response = $this->get(route('project.show', $project));

        $response->assertSuccessful();

        $taskData = $response->props()['tasks']['data'][0];
        $this->assertArrayHasKey('task_status', $taskData);
        $this->assertEquals('In Progress', $taskData['task_status']['name']);
        $this->assertEquals('#ff6b6b', $taskData['task_status']['color']);
    }

    /** @test */
    public function project_show_page_handles_tasks_without_status()
    {
        $project = Project::factory()->create();
        $task = Task::factory()->create([
            'project_id' => $project->id,
            'status_id' => null
        ]);

        $this->actingAsUser();

        $response = $this->get(route('project.show', $project));

        $response->assertSuccessful();

        $taskData = $response->props()['tasks']['data'][0];
        $this->assertNull($taskData['task_status']);
    }
}
