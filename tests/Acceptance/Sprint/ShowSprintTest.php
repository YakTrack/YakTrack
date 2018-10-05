<?php

use Illuminate\Foundation\Testing\DatabaseMigrations;

class ShowSprintTest extends BrowserKitTestCase
{
    use DatabaseMigrations;

    /** @test */
    public function a_logged_in_user_can_view_details_of_a_sprint()
    {
        // Create project and sprint
        $project = factory(App\Project::class)->create();
        $sprint = factory(App\Sprint::class)->create(['project_id' => $project->id]);

        // Login user
        $user = $this->actingAsUser();

        // Visit show sprint page
        $this->visit(route('sprint.show', ['sprint' => $sprint]));

        // Verify correct page has loaded
        $this->seePageIs(route('sprint.show', ['sprint' => $sprint]));

        // Verify sprint data is included in view
        $this->see($sprint->name);
        $this->see($project->name);
    }
}
