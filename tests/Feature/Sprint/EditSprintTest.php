<?php

namespace Tests\Feature\Sprint;

use App\Models\Project;
use App\Models\Sprint;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class EditSprintTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function a_user_can_view_the_page_to_edit_a_sprint()
    {
        $sprint = factory(Sprint::class)->create();

        $this->actingAsUser();

        $response = $this->get(route('sprint.edit', ['sprint' => $sprint]));

        $response->assertSuccessful();

        $response->assertSee($sprint->name);
    }

    /** @test */
    public function a_user_can_submit_a_patch_request_to_update_a_sprint()
    {
        $this->withoutExceptionHandling();
        $sprint = factory(Sprint::class)->create([
            'is_open' => 0,
        ]);

        $newProject = factory(Project::class)->create();

        $this->actingAsUser();

        $response = $this->patch(route('sprint.update', ['sprint' => $sprint]), $newSprintDetails = [
            'name'       => 'New sprint name',
            'project_id' => $newProject->id,
            'is_open'    => 'is_open',
        ]);

        $response->assertRedirect(route('sprint.index'));

        $this->assertDatabaseHas(
            'sprints',
            array_merge(
                $newSprintDetails,
                [
                    'id' => $sprint->id,
                    'is_open' => 1,
                ],
            )
        );
    }
}
