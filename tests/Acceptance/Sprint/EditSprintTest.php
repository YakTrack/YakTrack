<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class EditSprintTest extends TestCase
{
    use DatabaseMigrations;

    /** @test */
    public function a_logged_in_user_can_edit_a_sprint()
    {
        // Generate projects
        $projects = factory(App\Project::class, 2)->create();

        // Generate sprint
        $sprint = factory(App\Sprint::class)->create(['project_id' => $projects[0]->id]);

        // Generate new sprint data
        $newSprint = factory(App\Sprint::class)->make();

        // Login first user
        $user = $this->actingAsUser();

        // Visit page
        $this->visit(route('sprint.edit', ['sprint' => $sprint]));

        // Verify correct page loads
        $this->seePageIs(route('sprint.edit', ['sprint' => $sprint]));

        // Fill in form an submit
        $this->type($newSprint->name, 'name')
            ->select($projects[1]->id, 'project_id')
            ->press('Update');

        // Verify redirected to correct page
        $this->seePageIs(route('sprint.index'));

        // Verify sprint update in database
        $this->seeInDatabase('sprints', [
            'id' => $sprint->id,
            'name' => $newSprint->name,
            'project_id' => $projects[1]->id
        ]);
    }
}
