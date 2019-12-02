<?php

namespace Tests\Feature\Client;

use App\Models\Client;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class IndexClientTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function a_user_can_see_a_list_of_clients()
    {
        $client = factory(Client::class)->create();

        $this->actingAsUser();

        $response = $this->get(route('client.index'));

        $response->assertSuccessful();

        $response->assertSee($client->name);
    }
}
