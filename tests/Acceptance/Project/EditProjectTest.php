<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class EditProjectTest extends BrowserKitTestCase
{
    use DatabaseMigrations;

    /** @test */
    public function a_logged_in_user_can_edit_a_project()
    {
        // Create clients
        $clients = factory(App\Client::class, 2)->create();

        // Create project
        $project = factory(App\Project::class)->create(['client_id' => $clients[0]->id]);

        // Login user
        $user = $this->actingAsUser();

        // Visit edit project page
        $this->visit(route('project.edit', ['project' => $project]));

        // Verify correct page has loaded
        $this->seePageIs(route('project.edit', ['project' => $project]));

        // Add new project information and press update
        $this->type('Updated Project', 'name')
            ->type('Updated project description.', 'description')
            ->select($clients[1]->id, 'client_id')
            ->press('Update');

        // Verify redirected to correct page
        $this->seePageIs(route('project.index'));

        // Verify databse updated
        $this->seeInDatabase('projects', [
            'name' => 'Updated Project',
            'description' => 'Updated project description.',
            'client_id' => $clients[1]->id,
        ]);
    }
}
