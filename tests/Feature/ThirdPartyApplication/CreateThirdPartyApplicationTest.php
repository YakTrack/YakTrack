<?php

it('can create a third party application', function () {
    $this->withoutExceptionHandling();

    $this->actingAsUser();

    $response = $this->post(route('third-party-application.store'), [
        'type' => 'wrike',
        'name' => 'Test Wrike Account',
    ]);

    $response->assertRedirect(route('third-party-application.index'));

    $this->assertDatabaseHas('third_party_applications', [
        'type' => 'wrike',
        'name' => 'Test Wrike Account',
    ]);
});
