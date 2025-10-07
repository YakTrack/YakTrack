<?php

use App\Models\User;

it('can load the login page', function () {
    $this->withoutExceptionHandling();

    $response = $this->get('/login');

    $response->assertSuccessful();
});

it('logs in when a guest submits the login form with correct credentials', function () {
    $this->withoutExceptionHandling();

    $user = User::factory()->create([
        'email'    => 'test@domain.com',
        'password' => Hash::make('password'),
    ]);

    $response = $this->post('/login', ['email' => 'test@domain.com', 'password' => 'password']);

    $response->assertRedirect('/');

    expect(Auth::check())->toBeTrue();
});