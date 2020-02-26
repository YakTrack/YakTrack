<?php

namespace Tests\Feature\Session;

use App\Models\Session;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class StopSessionTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function a_user_can_stop_a_session_from_now_with_a_post_request()
    {
        $session = factory(Session::class)->states('is_running')->create();

        $this->actingAsUser();

        $response = $this->post(route('session.stop', ['session' => $session]));

        $response->assertRedirect(route('session.index'));

        $this->assertFalse($session->fresh()->isRunning());
    }
}
