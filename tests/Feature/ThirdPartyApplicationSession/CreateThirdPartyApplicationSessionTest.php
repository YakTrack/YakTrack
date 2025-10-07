<?php

use App\Models\Session;
use App\Models\ThirdPartyApplication;

it('can export a session to a third party application', function () {
    $thirdPartyApplication = ThirdPartyApplication::factory()->wrike()->create();

    $session = Session::factory()->create();

    $session->exportToThirdPartyApplication($thirdPartyApplication);

    expect($session->thirdPartyApplicationSessions->contains(function ($thirdPartyApplicationSession) use ($session) {
        return $thirdPartyApplicationSession->session->id === $session->id;
    }))->toBeTrue();
});

it('can create a session with a post request', function () {
    $this->withoutExceptionHandling();

    $thirdPartyApplication = ThirdPartyApplication::factory()->wrike()->create();

    $session = Session::factory()->create();

    $this->actingAsUser();

    $response = $this->post(route('third-party-application-session.store'), [
        'session_id'                 => $session->id,
        'third_party_application_id' => $thirdPartyApplication->id,
    ]);

    $response->assertRedirect('/');

    expect($session->thirdPartyApplicationSessions->contains(function ($thirdPartyApplicationSession) use ($session) {
        return $thirdPartyApplicationSession->session->id === $session->id;
    }))->toBeTrue();
});
