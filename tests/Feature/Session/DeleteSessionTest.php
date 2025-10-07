<?php

use App\Models\Session;

it('can delete a session', function () {
    $session = Session::factory()->create();

    $this->actingAsUser();

    $response = $this->delete(route('session.destroy', ['session' => $session]));

    $response->assertRedirect(route('session.index'));

    $this->assertDatabaseMissing('sessions', [
        'id' => $session->id,
    ]);
});