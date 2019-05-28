<?php

use App\Models\Session;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Tests\TestCase;

class IndexSessionTest extends TestCase
{
    use DatabaseMigrations;

    /** @test */
    public function when_no_per_page_parameter_is_present_the_user_is_redirected()
    {
        $this->withoutExceptionHandling();

        $this->actingAsUser();

        $response = $this->get(route('session.index'));

        $response->assertRedirect(route('session.index', ['per-page' => 100]));
    }

    /** @test */
    public function a_user_can_load_the_session_index_page()
    {
        $this->withoutExceptionHandling();

        $this->actingAsUser();

        $response = $this->get(route('session.index', ['per-page' => 100]));

        $response->assertSuccessful();

        $response->assertViewIs('session.index');
    }
}
