<?php

namespace Tests\Feature\Session;

use App\Models\Target;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class IndexTargetTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function a_user_can_load_the_target_index_page()
    {
        $target = factory(Target::class)->create();

        $this->withoutExceptionHandling();

        $this->actingAsUser();

        $response = $this->get(route('target.index'));

        $response->assertSuccessful();
    }
}
