<?php

namespace Tests\Feature\ThirdPartyApplication;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class CreateThirdPartyApplicationTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function a_user_can_create_an_third_party_application()
    {
        $this->withoutExceptionHandling();

        $this->actingAsUser();

        $response = $this->post(route('external-task-manager.store'), [
            'type' => 'wrike',
            'name' => 'Test Wrike Account',
        ]);

        $response->assertRedirect(route('external-task-manager.index'));

        $this->assertDatabaseHas('third_party_applications', [
            'type' => 'wrike',
            'name' => 'Test Wrike Account'
        ]);
    }
}
