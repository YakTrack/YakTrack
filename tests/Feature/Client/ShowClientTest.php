<?php

namespace Tests\Feature\Client;

use App\Models\Client;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ShowClientTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function a_user_can_see_a_single_client()
    {
        $client = factory(Client::class)->create();

        $this->actingAsUser();

        $response = $this->get(route('client.show', ['client' => $client]));

        $response->assertSuccessful();

        $response->assertSee($client->name);
        $response->assertSee($client->email);
    }
}
