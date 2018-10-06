<?php

use Illuminate\Foundation\Testing\DatabaseMigrations;

class EditTaskTest extends BrowserKitTestCase
{
    use DatabaseMigrations;

    /** @test */
    public function a_user_can_edit_a_task()
    {
        // Create projects
        $projects = factory(App\Models\Project::class, 2)->create();

        // Create task
        $task = factory(App\Models\Task::class)->create(['project_id' => $projects[0]->id]);

        // Login user
        $user = $this->actingAsUser();

        // Visit edit task page
        $this->visit(route('task.edit', ['task' => $task]));

        // Verify correct page has loaded
        $this->seePageIs(route('task.edit', ['task' => $task]));

        // Add new task information and press update
        $this->type('Updated Task', 'name')
            ->type('Updated task description.', 'description')
            ->select($projects[1]->id, 'project_id')
            ->press('Update');

        // Verify redirected to correct page
        $this->seePageIs(route('task.index'));

        // Verify databse updated
        $this->seeInDatabase('tasks', [
            'name'        => 'Updated Task',
            'description' => 'Updated task description.',
            'project_id'  => $projects[1]->id,
        ]);
    }
}
