<?php

namespace Tests\Feature\Client;

use App\Models\Client;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class EditClientTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function a_user_can_see_the_page_to_edit_a_client()
    {
        $client = factory(Client::class)->create();

        $this->actingAsUser();

        $response = $this->get(route('client.edit', ['client' => $client]));

        $response->assertSuccessful();
    }

    /** @test */
    public function a_user_can_submit_a_put_request_to_update_a_client()
    {
        $client = factory(Client::class)->create();

        $this->actingAsUser();

        $response = $this->put(route('client.update', ['client' => $client]), $newClientDetails = [
            'name'  => 'New name',
            'email' => 'test@domain.com',
        ]);

        $response->assertRedirect(route('client.index'));

        $this->assertDatabaseHas('clients', array_merge(['id' => $client->id], $newClientDetails));
    }
}
