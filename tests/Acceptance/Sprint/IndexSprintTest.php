<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class IndexSprintTest extends TestCase
{
    use DatabaseMigrations;
    
    /** @test */
    public function a_logged_in_user_can_view_their_sprints()
    {
        // Create project
        $project = App\Project::create();

        // Create sprints
        $sprints = factory(App\Sprint::class)->create(['project_id' => $project->id]);

        // Login first user
        $user = $this->actingAsUser();

        // Navigate to sprints index page from home page
        $this->visit(route('home'))
            ->click('Sprints');

        // Verify correct page loads
        $this->seePageIs(route('sprint.index'));

        // Verify sprints are visible
        $sprints->each( function($sprint) {
            $this->see($sprint->name);
        });
    }
}
