<?php

use App\Project;
use App\Sprint;
use App\Task;
use Illuminate\Foundation\Testing\DatabaseMigrations;

class CreateTaskTest extends BrowserKitTestCase
{
    use DatabaseMigrations;

    /** @test */
    public function a_logged_in_user_can_create_a_task()
    {
        $this->actingAsUser();

        // Create project
        $project = factory(Project::class)->create();

        // Create sprint
        $sprint = factory(Sprint::class)->create(['project_id' => $project->id]);

        // Create parent task
        $parentTask = factory(Task::class)->create([
            'parent_id' => $project->id,
            'sprint_id' => $sprint->id,
        ]);

        // Visit page
        $this->visit(route('task.create'));

        // Verify correct page loads
        $this->seePageIs(route('task.create'));

        // Simulate form submission
        $response = $this->post(route('task.store'), [
            'name'        => 'Test Task',
            'description' => 'Test task description.',
            'project_id'  => $project->id,
            'sprint_id'   => $sprint->id,
            'parent_id'   => $parentTask->id,
        ]);

        // Verify redirected to correct page
        $this->followRedirects();
        $this->seePageIs(route('task.index'));

        // Verify that we see what we expect on the page
        $this->see('Test Task');
        $this->see($project->name);

        // Verify task added to database
        $this->seeInDatabase('tasks', [
            'name'        => 'Test Task',
            'description' => 'Test task description.',
            'project_id'  => $project->id,
            'parent_id'   => $parentTask->id,
            'sprint_id'   => $sprint->id,
        ]);
    }
}
