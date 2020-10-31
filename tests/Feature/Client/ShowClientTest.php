<?php

namespace Tests\Feature\Client;

use App\Models\Client;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ShowClientTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function a_user_can_see_a_single_client()
    {
        $client = factory(Client::class)->create([
            'name' => 'Joseph O\'Conner',
        ]);

        $this->actingAsUser();

        $response = $this->get(route('client.show', ['client' => $client]));

        $response->assertSuccessful();

        $response->assertSee(e($client->name));
        $response->assertSee($client->email);
    }
}
