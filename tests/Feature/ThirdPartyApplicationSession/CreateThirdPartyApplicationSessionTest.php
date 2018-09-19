<?php

namespace Tests\Feature\ThirdPartyApplicationSession;

use App\Models\ThirdPartyApplication;
use App\Session;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class CreateThirdPartyApplicationSessionTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function a_session_can_be_exported_to_an_third_party_application_with_sessions()
    {
        $thirdPartyApplication = factory(ThirdPartyApplication::class)->states(['wrike'])->create();

        $session = factory(Session::class)->create();

        $session->exportToThirdPartyApplication($thirdPartyApplication);

        $this->assertTrue($session->thirdPartyApplicationSessions->contains(function ($thirdPartyApplicationSession) use ($session) {
            return $thirdPartyApplicationSession->session->id === $session->id;
        }));
    }
}
