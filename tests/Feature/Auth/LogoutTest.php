<?php

namespace Tests\Feature\Auth;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Auth;
use Tests\TestCase;

class LogoutTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function a_user_can_submit_a_post_request_to_the_logout_route_to_logout()
    {
        $this->withoutExceptionHandling();

        $this->actingAsUser();

        $response = $this->post('/logout');

        $response->assertRedirect('/login');

        $this->assertFalse(Auth::check());
    }
}
