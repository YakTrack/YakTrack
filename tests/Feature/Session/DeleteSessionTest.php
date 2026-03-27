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

it('can delete multiple selected sessions', function () {
    $sessions = Session::factory()->count(3)->create();

    $this->actingAsUser();

    $response = $this->post(route('sessions.destroy-many'), [
        'session_ids' => $sessions->pluck('id')->all(),
    ]);

    $response->assertRedirect();

    foreach ($sessions as $session) {
        $this->assertDatabaseMissing('sessions', [
            'id' => $session->id,
        ]);
    }
});

it('validates session ids when deleting multiple sessions', function () {
    $this->actingAsUser();

    $response = $this->post(route('sessions.destroy-many'), [
        'session_ids' => [],
    ]);

    $response->assertSessionHasErrors('session_ids');
});

it('validates that session ids exist when deleting multiple sessions', function () {
    $this->actingAsUser();

    $response = $this->post(route('sessions.destroy-many'), [
        'session_ids' => [999999],
    ]);

    $response->assertSessionHasErrors('session_ids.0');
});
