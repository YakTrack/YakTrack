<?php

namespace Tests\Feature\Sprint;

use App\Models\Project;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CreateSprintTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function a_user_can_view_the_form_to_create_a_sprint()
    {
        $project = factory(Project::class)->create();

        $this->actingAsUser();

        $response = $this->get(route('sprint.create'));

        $response->assertSuccessful();

        $response->assertSee($project->name);
    }

    /** @test */
    public function a_user_can_store_a_new_sprint_with_a_post_request()
    {
        $project = factory(Project::class)->create();

        $this->actingAsUser();

        $response = $this->post(route('sprint.store'), $sprintDetails = [
            'name'       => 'New sprint',
            'project_id' => $project->id,
            'is_open'    => array_random([0, 1]),
        ]);

        $response->assertRedirect(route('sprint.index'));

        $this->assertDatabaseHas('sprints', $sprintDetails);
    }
}
