<?php

use App\Models\Session;
use Carbon\Carbon;

it('can start a session from now with a post request', function () {
    $previouslyRunningSession = Session::factory()->running()->create();

    $this->actingAsUser();

    Carbon::setTestNow(Carbon::parse('2018-01-01 12:34:56'));

    $response = $this->post(route('session.start'));

    $response->assertRedirect(route('session.index'));

    $this->assertDatabaseHas('sessions', [
        'started_at' => '2018-01-01 12:34:56',
    ]);

    expect($previouslyRunningSession->fresh()->isRunning())->toBeFalse();

    Carbon::setTestNow();
});