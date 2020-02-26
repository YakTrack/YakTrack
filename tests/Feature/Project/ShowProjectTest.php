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

        $response->assertSee($project->name);
        $response->assertSee($project->description);
        $response->assertSee($project->client->name);
    }
}
