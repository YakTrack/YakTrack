<?php

use App\Models\Session;
use Carbon\Carbon;

it('can end a session with a post request', function () {
    Carbon::setTestNow(Carbon::parse('2018-01-01 00:10:00'));

    $this->withoutExceptionHandling();

    $session = Session::factory()->create([
        'started_at' => '2018-01-01 00:00:00',
        'ended_at'   => null,
    ]);

    $this->actingAsUser();

    $response = $this->post(route('session.stop'));

    $response->assertRedirect(route('session.index'));

    $this->assertDatabaseHas('sessions', [
        'started_at' => '2018-01-01 00:00:00',
        'ended_at'   => '2018-01-01 00:10:00',
    ]);

    Carbon::setTestNow();
});