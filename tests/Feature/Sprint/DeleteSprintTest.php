<?php

use App\Models\Sprint;

it('can delete a sprint', function () {
    $sprint = Sprint::factory()->create();

    $this->actingAsUser();

    $response = $this->delete(route('sprint.destroy', [
        'sprint' => $sprint,
    ]));

    $response->assertRedirect(route('sprint.index'));

    $this->assertDatabaseMissing('sprints', [
        'id' => $sprint->id,
    ]);
});
