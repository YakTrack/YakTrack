<?php

use App\Models\Session;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Tests\TestCase;

class IndexSessionTest extends TestCase
{
    use DatabaseMigrations;

    /** @test */
    public function a_user_can_load_the_session_index_page()
    {
        $this->withoutExceptionHandling();

        $this->actingAsUser();

        $response = $this->get(route('session.index'));

        $response->assertSuccessful();

        $response->assertViewIs('session.index');
    }
}
