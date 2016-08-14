<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class ShowSprintTest extends TestCase
{
    use DatabaseTransactions;

    /** @test */
    public function a_logged_in_user_can_view_details_of_a_sprint()
    {
        // Create project and sprint
        $project = factory(App\Project::class)->create();
        $sprint = factory(App\Sprint::class)->create(['project_id' => $project->id]);

        // Login user
        \Auth::login(App\User::first());

        // Visit show sprint page
        $this->visit(route('sprint.show', ['sprint' => $sprint]));

        // Verify correct page has loaded
        $this->seePageIs(route('sprint.show', ['sprint' => $sprint]));

        // Verify sprint data is included in view
        $this->see($sprint->name);
        $this->see($project->name);
    }
}
