<?php

namespace Tests\Feature\Project;

use App\Models\Project;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class IndexProjectTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function a_user_can_see_a_list_of_projects()
    {
        $this->withoutExceptionHandling();

        $invoice = factory(Project::class)->create();

        $this->actingAsUser();

        $response = $this->get(route('project.index'));

        $response->assertSuccessful();

        $response->assertSee($invoice->number);
    }
}
