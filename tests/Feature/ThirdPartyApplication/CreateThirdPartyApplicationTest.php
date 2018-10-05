<?php

namespace Tests\Feature\ThirdPartyApplication;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CreateThirdPartyApplicationTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function a_user_can_create_an_third_party_application()
    {
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
    }
}
