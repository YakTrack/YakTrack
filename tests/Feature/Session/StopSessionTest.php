<?php

use App\Models\Session;

it('can stop a session from now with a post request', function () {
    $session = Session::factory()->running()->create();

    $this->actingAsUser();

    $response = $this->post(route('session.stop', ['session' => $session]));

    $response->assertRedirect(route('session.index'));

    expect($session->fresh()->isRunning())->toBeFalse();
});