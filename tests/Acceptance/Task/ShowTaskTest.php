<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class ShowTaskTest extends BrowserKitTestCase
{
    use DatabaseMigrations;

    /** @test */
    public function a_user_can_view_the_show_task_page_for_a_task()
    {
        // Create task
        $task = factory(App\Task::class)->create();

        // Login user
        $user = $this->actingAsUser();

        // Visit show task page
        $this->visit(route('task.show', ['task' => $task]));

        // Verify correct page has loaded
        $this->seePageIs(route('task.show', ['task' => $task]));

        // Verify task data is included in view
        $this->see($task->name);
        $this->see($task->description);
    }
}
