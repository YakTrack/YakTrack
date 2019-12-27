<?php

namespace Tests\Feature\Project;

use App\Models\Client;
use App\Models\Project;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Tests\TestCase;

class EditProjectTest extends TestCase
{
    use DatabaseMigrations;

    /** @test */
    public function a_user_can_view_the_page_to_edit_a_project()
    {
        $clients = factory(Client::class, 2)->create();
        $project = factory(Project::class)->create(['client_id' => $clients[0]->id]);

        $this->actingAsUser();

        $response = $this->get(route('project.edit', ['project' => $project]));

        $response->assertSuccessful();

        $response->assertSee($project->name);
        $clients->each(function ($client) use ($response) {
            $response->assertSee($client->name);
        });
    }

    /** @test */
    public function a_user_can_update_a_project_with_a_patch_request()
    {
        $clients = factory(Client::class, 2)->create();
        $project = factory(Project::class)->create(['client_id' => $clients[0]->id]);

        $this->actingAsUser();

        $response = $this->patch(
            route('project.update', ['project' => $project]),
            $updatedProjectDetails = [
                'name'        => 'Updated Project',
                'description' => 'Updated project description',
                'client_id'   => $clients[1]->id,
            ]
        );

        $response->assertRedirect(route('project.index'));

        $this->assertDatabaseHas('projects', $updatedProjectDetails);
    }
}
