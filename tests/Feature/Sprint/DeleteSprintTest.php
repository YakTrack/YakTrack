<?php

namespace Tests\Feature\Sprint;

use App\Models\Sprint;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DeleteSprintTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function a_user_can_delete_a_sprint()
    {
        $sprint = factory(Sprint::class)->create();

        $this->actingAsUser();

        $response = $this->delete(route('sprint.destroy', [
            'sprint' => $sprint,
        ]));

        $response->assertRedirect(route('sprint.index'));

        $this->assertDatabaseMissing('sprints', [
            'id' => $sprint->id,
        ]);
    }
}
