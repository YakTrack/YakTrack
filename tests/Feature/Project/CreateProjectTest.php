<?php

namespace Tests\Feature\Project;

use App\Models\Client;
use App\Models\Project;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CreateProjectTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function a_user_can_visit_the_create_project_page()
    {
        $client = factory(Client::class)->create();
        $this->withoutExceptionHandling();

        $this->actingAsUser();

        $response = $this->get(route('project.create'));

        $response->assertSuccessful();

        $response->assertSee($client->name);
    }

    /** @test */
    public function a_user_can_submit_a_post_request_to_create_a_project()
    {
        $client = factory(Client::class)->create();

        $this->withoutExceptionHandling();

        $this->actingAsUser();

        $response = $this->post(route('project.store'), [
            'name' => 'Test Project',
            'description' => 'Test project description.',
            'client_id' => $client->id,
        ]);

        $response->assertRedirect(route('project.index'));

        $this->assertDatabaseHas('projects', [
            'name'        => 'Test Project',
            'description' => 'Test project description.',
            'client_id'   => $client->id,
        ]);
    }
}
