<?php

it('returns the current csrf token to authenticated users', function () {
    $this->actingAsUser();

    $response = $this->getJson('/csrf-token');

    $response->assertSuccessful();
    $response->assertExactJson([
        'token' => csrf_token(),
    ]);
});

it('requires authentication', function () {
    $response = $this->getJson('/csrf-token');

    $response->assertUnauthorized();
});
