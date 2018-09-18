<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class DeleteSprintTest extends BrowserKitTestCase
{
    use DatabaseMigrations;

    /** @test */
    public function a_logged_in_user_can_delete_a_sprint()
    {
        // Login first user
        $user = $this->actingAsUser();

        // Generate project and sprint
        $project = factory(App\Project::class)->create();
        $sprint = factory(App\Sprint::class)->create(['project_id' => $project->id]);

        // Visit route
        $this->delete(route('sprint.destroy', [
            'sprint' => $sprint
        ]));

        // Verify redirected to correct page
        $this->followRedirects();
        $this->seePageIs(route('sprint.index'));

        // Verify sprint added to database
        $this->dontSeeInDatabase('sprints', [
            'id' => $sprint->id
        ]);
    }
}
