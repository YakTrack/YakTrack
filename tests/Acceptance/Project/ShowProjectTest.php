<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class ShowProjectTest extends TestCase
{
    use DatabaseTransactions;

    /** @test */
    public function a_logged_in_user_can_view_details_of_a_project()
    {
        // Create project
        $project = factory(App\Project::class)->create();

        // Login user
        \Auth::login(App\User::first());

        // Visit show project page
        $this->visit(route('project.show', ['project' => $project]));

        // Verify correct page has loaded
        $this->seePageIs(route('project.show', ['project' => $project]));

        // Verify project data is included in view
        $this->see($project->name);
        $this->see($project->description);
    }

}
