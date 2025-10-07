<?php

use App\Models\Target;

it('can load the target index page', function () {
    $target = Target::factory()->create();

    $this->withoutExceptionHandling();

    $this->actingAsUser();

    $response = $this->get(route('target.index'));

    $response->assertSuccessful();
});