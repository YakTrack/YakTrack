<?php

use App\Models\Task;
use Illuminate\Foundation\Testing\DatabaseMigrations;

class DeleteTaskTest extends BrowserKitTestCase
{
    use DatabaseMigrations;

    /** @test */
    public function a_user_can_delete_a_task()
    {
        // Login first user
        $user = $this->actingAsUser();

        // Generate task
        $task = factory(Task::class)->create(['name' => 'Test Task']);

        // Visit route
        $this->delete(route('task.destroy', [
            'task' => $task,
        ]));

        // Verify redirected to correct page
        $this->followRedirects();
        $this->seePageIs(route('task.index'));

        // Verify task removed from database
        $this->dontSeeInDatabase('tasks', [
            'id' => $task->id,
        ]);
    }
}
