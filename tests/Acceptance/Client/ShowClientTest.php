<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class ShowClientTest extends BrowserKitTestCase
{
    use DatabaseMigrations;

    /** @test */
    public function a_logged_in_user_can_view_details_of_a_client()
    { 
        // Create client
        $client = factory(App\Client::class)->create();

        // Login user
        $user = $this->actingAsUser();

        // Visit show client page
        $this->visit(route('client.show', ['client' => $client]));

        // Verify correct page has loaded
        $this->seePageIs(route('client.show', ['client' => $client]));

        // Verify client data is included in view
        $this->see($client->name);
        $this->see($client->email);
    }
}
