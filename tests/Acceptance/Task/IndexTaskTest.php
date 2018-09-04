<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class IndexTaskTest extends TestCase
{
    use DatabaseMigrations;

    /** @test */
    public function testExample()
    {
        // Create project
        $project = factory(App\Project::class)->create();

        // Create tasks
        $tasks = factory(App\Task::class)->create(['project_id' => $project->id]);

        // Login first user
        $user = $this->actingAsUser();

        // Navigate to tasks index page from home page
        $this->visit(route('home'))
            ->click('Tasks');

        // Verify correct page loads
        $this->seePageIs(route('task.index'));

        // Verify tasks are visible
        $tasks->each( function($task) {
            $this->see($task->name);
        });
    }
}
