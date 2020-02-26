<?php

namespace Tests\Feature\Client;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CreateClientTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function a_user_can_load_the_page_to_create_a_client()
    {
        $this->actingAsUser();

        $response = $this->get(route('client.create'));

        $response->assertSuccessful();
    }

    /** @test */
    public function a_user_can_submit_a_post_request_to_create_a_client()
    {
        $this->actingAsUser();

        $response = $this->post(route('client.store'), $newClientDetails = [
            'name'  => 'Test Client',
            'email' => 'test@domain.com',
        ]);

        $response->assertRedirect(route('client.index'));

        $this->assertDatabaseHas('clients', $newClientDetails);
    }
}
