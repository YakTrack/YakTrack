<?php

namespace Tests\Feature\Client;

use App\Models\Client;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\DatabaseMigrations;

class DeleteClientTest extends TestCase
{
    use DatabaseMigrations;

    /** @test */
    public function a_user_can_delete_a_client()
    {
        $client = factory(Client::class)->create();

        $this->actingAsUser();

        $response = $this->delete(route('client.destroy', [
            'client' => $client,
        ]));

        $response->assertRedirect(route('client.index'));

        $this->assertDatabaseMissing('clients', [
            'id' => $client->id,
        ]);
    }
}
