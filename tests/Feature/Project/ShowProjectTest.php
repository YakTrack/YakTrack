<?php

namespace Tests\Feature\Project;

use App\Models\Project;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ShowProjectTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function a_logged_in_user_can_view_details_of_a_project()
    {
        $project = factory(Project::class)->create();

        $this->actingAsUser();

        $response = $this->get(route('project.show', ['project' => $project]));

        $response->assertSuccessful();

        // escape the apostrophe in the same manner as blade is
        $response->assertSee(e($project->name));

        $response->assertSee($project->description);
        $response->assertSee(e($project->client->name));
    }

    /** @test */
    public function a_logged_in_user_can_view_details_of_a_project_with_an_apostrophe_in_the_name()
    {
        $project = factory(Project::class)->create(['name' => 'Steve\'s Test Project']);

        $this->actingAsUser();

        $response = $this->get(route('project.show', ['project' => $project]));

        $response->assertSuccessful();

        // escape the apostrophe in the same manner as blade is
        $response->assertSee(e($project->name));

        $response->assertSee($project->description);
        $response->assertSee(e($project->client->name));
    }
}
