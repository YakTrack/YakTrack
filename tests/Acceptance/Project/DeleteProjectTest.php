<?php

use App\Project;
use Illuminate\Foundation\Testing\DatabaseMigrations;

class DeleteProjectTest extends BrowserKitTestCase
{
    use DatabaseMigrations;

    /** @test */
    public function a_logged_in_user_can_delete_a_project()
    {
        // Login first user
        $user = $this->actingAsUser();

        // Generate project
        $project = factory(Project::class)->create(['name' => 'Test Project']);

        // Visit route
        $this->delete(route('project.destroy', [
            'project' => $project,
        ]));

        // Verify redirected to correct page
        $this->followRedirects();
        $this->seePageIs(route('project.index'));

        // Verify project removed from database
        $this->dontSeeInDatabase('projects', [
            'id' => $project->id,
        ]);
    }
}
