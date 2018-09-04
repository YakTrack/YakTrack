<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class CreateTaskTest extends TestCase
{
    use DatabaseMigrations;

    /** @test */
    public function a_logged_in_user_can_create_a_task()
    {
        // Create project
        $project = App\Project::create();
        
        // Create sprint
        $sprint = App\Project::create(['project_id' => $project->id]);

        // Create parent task
        $parentTask = App\Task::create([
            'parent_id' => $project->id,
            'parent_type' => 'App\\Project',
            'sprint_id' => $sprint->id,
        ]);

        // Login first user
        \Auth::login(App\User::first());

        // Visit page
        $this->visit(route('task.create'));

        // Verify correct page loads
        $this->seePageIs(route('task.create'));

        // Fill in form an submit
        $this->type('Test Task', 'name')
            ->type('Test task description.', 'description')
            ->select($sprint->id, 'sprint_id')
            ->select($parentTask->id, 'task_id')
            ->press('Create');

        // Verify redirected to correct page
        $this->seePageIs(route('task.index'));

        // Verify task added to database
        $this->seeInDatabase('tasks', [
            'name' => 'Test Task',
            'description' => 'Test task description.',
            'parent_id' => $parentTask->id,
            'parent_type' => 'App\\Task',
            'sprint_id' => $sprint->id
        ]);
    }
}
