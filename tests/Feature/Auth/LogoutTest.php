<?php

use Illuminate\Support\Facades\Auth;

it('can submit a post request to the logout route to logout', function () {
    $this->withoutExceptionHandling();

    $this->actingAsUser();

    $response = $this->post('/logout');

    $response->assertRedirect('/login');

    expect(Auth::check())->toBeFalse();
});
