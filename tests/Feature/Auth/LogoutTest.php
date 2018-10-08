<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use Auth;
use Hash;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LogoutTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function a_guest_can_submit_a_post_request_to_the_logout_route_to_logout()
    {
        $this->withoutExceptionHandling();

        $this->actingAsUser();

        $response = $this->post('/logout');

        $response->assertRedirect('/');

        $this->assertFalse(Auth::check());
    }
}
