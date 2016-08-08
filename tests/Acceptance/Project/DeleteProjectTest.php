<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;

use App\Project;

class DeleteProjectTest extends TestCase
{
    use DatabaseTransactions;

    /** @test */
    public function a_logged_in_user_can_delete_a_project()
    {
        // Login first user
        \Auth::login(App\User::first());

        // Generate project
        $project = Project::create(['name' => 'Test Project']);

        // Visit route 
        $this->delete(route('project.destroy', [
            'project' => $project
        ]));

        // Verify redirected to correct page
        $this->followRedirects();
        $this->seePageIs(route('project.index'));

        // Verify project removed from database
        $this->dontSeeInDatabase('projects', [
            'id' => $project->id
        ]);
    }
}
