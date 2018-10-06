<?php

namespace Tests\Feature\ThirdPartyApplicationSession;

use App\Models\Session;
use App\Models\ThirdPartyApplication;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CreateThirdPartyApplicationSessionTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function a_session_can_be_exported_to_a_third_party_application_with_sessions()
    {
        $thirdPartyApplication = factory(ThirdPartyApplication::class)->states(['wrike'])->create();

        $session = factory(Session::class)->create();

        $session->exportToThirdPartyApplication($thirdPartyApplication);

        $this->assertTrue($session->thirdPartyApplicationSessions->contains(function ($thirdPartyApplicationSession) use ($session) {
            return $thirdPartyApplicationSession->session->id === $session->id;
        }));
    }

    /** @test */
    public function a_session_can_be_created_with_a_post_request()
    {
        $this->withoutExceptionHandling();

        $thirdPartyApplication = factory(ThirdPartyApplication::class)->states(['wrike'])->create();

        $session = factory(Session::class)->create();

        $this->actingAsUser();

        $response = $this->post(route('third-party-application-session.store'), [
            'session_id'                 => $session->id,
            'third_party_application_id' => $thirdPartyApplication->id,
        ]);

        $response->assertRedirect('/');

        $this->assertTrue($session->thirdPartyApplicationSessions->contains(function ($thirdPartyApplicationSession) use ($session) {
            return $thirdPartyApplicationSession->session->id === $session->id;
        }));
    }
}
