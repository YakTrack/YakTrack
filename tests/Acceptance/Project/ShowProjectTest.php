<?php

use Illuminate\Foundation\Testing\DatabaseMigrations;

class ShowProjectTest extends BrowserKitTestCase
{
    use DatabaseMigrations;

    /** @test */
    public function a_logged_in_user_can_view_details_of_a_project()
    {
        // Create project
        $project = factory(App\Project::class)->create();

        // Login user
        $user = $this->actingAsUser();

        // Visit show project page
        $this->visit(route('project.show', ['project' => $project]));

        // Verify correct page has loaded
        $this->seePageIs(route('project.show', ['project' => $project]));

        // Verify project data is included in view
        $this->see($project->name);
        $this->see($project->description);
    }
}
