<?php

namespace Tests\Feature\Sprint;

use App\Models\Project;
use App\Models\Sprint;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Tests\TestCase;

class IndexSprintTest extends TestCase
{
    use DatabaseMigrations;

    /** @test */
    public function a_user_can_view_a_list_of_sprints()
    {
        $project = factory(Project::class)->create();
        $sprints = factory(Sprint::class)->create(['project_id' => $project->id]);

        $this->actingAsUser();

        $response = $this->get(route('sprint.index'));

        $response->assertSuccessful();

        $sprints->each(function ($sprint) use ($response) {
            $response->assertSee($sprint->name);
        });
    }
}
