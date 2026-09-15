<?php

use App\Models\User;
use Illuminate\Support\Facades\Hash;

it('can log in after a failed attempt with incorrect credentials', function () {
    User::factory()->create([
        'email'    => 'test@domain.com',
        'password' => Hash::make('password'),
    ]);

    $page = visit('/login');

    $page->fill('email', 'test@domain.com')
        ->fill('password', 'wrong-password')
        ->click('Login')
        ->assertSee('These credentials do not match our records');

    $page->fill('email', 'test@domain.com')
        ->fill('password', 'password')
        ->click('Login')
        ->assertNoJavaScriptErrors();

    $this->assertAuthenticated();
});
