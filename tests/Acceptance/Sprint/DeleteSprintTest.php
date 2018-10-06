<?php

use Illuminate\Foundation\Testing\DatabaseMigrations;

class DeleteSprintTest extends BrowserKitTestCase
{
    use DatabaseMigrations;

    /** @test */
    public function a_logged_in_user_can_delete_a_sprint()
    {
        // Login first user
        $user = $this->actingAsUser();

        // Generate project and sprint
        $project = factory(App\Models\Project::class)->create();
        $sprint = factory(App\Models\Sprint::class)->create(['project_id' => $project->id]);

        // Visit route
        $this->delete(route('sprint.destroy', [
            'sprint' => $sprint,
        ]));

        // Verify redirected to correct page
        $this->followRedirects();
        $this->seePageIs(route('sprint.index'));

        // Verify sprint added to database
        $this->dontSeeInDatabase('sprints', [
            'id' => $sprint->id,
        ]);
    }
}
