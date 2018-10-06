<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use Auth;
use Hash;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LoginTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function a_guest_can_load_the_login_page()
    {
        $this->withoutExceptionHandling();

        $response = $this->get('/login');

        $response->assertSuccessful();
    }

    /** @test */
    public function when_a_guest_submits_the_login_form_with_correct_credentials_they_are_logged_in()
    {
        $this->withoutExceptionHandling();

        $user = factory(User::class)->create([
            'email'    => 'test@domain.com',
            'password' => Hash::make('password'),
        ]);

        $response = $this->post('/login', ['email' => 'test@domain.com', 'password' => 'password']);

        $response->assertRedirect('/');

        $this->assertTrue(Auth::check());
    }
}
