<?php

use App\Models\Session;

it('can edit multiple sessions with a patch request', function () {
    $this->withoutExceptionHandling();

    $sessions = Session::factory()->count(2)->create([
        'is_billable' => 0,
    ]);

    $this->actingAsUser();

    $response = $this->patch(
        route('sessions.update'),
        [
            'sessions' => [
                $sessions[0]->id => [
                    'is_billable' => 1,
                ],
                $sessions[1]->id => [
                    'is_billable' => 1,
                ],
            ],
        ]
    );

    $response->assertRedirect('/');

    $this->assertDatabaseHas('sessions', [
        'id'          => $sessions[0]->id,
        'is_billable' => 1,
    ]);

    $this->assertDatabaseHas('sessions', [
        'id'          => $sessions[1]->id,
        'is_billable' => 1,
    ]);
});