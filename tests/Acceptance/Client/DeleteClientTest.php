<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class DeleteClientTest extends TestCase
{
    use DatabaseMigrations;

    /** @test */
    public function a_logged_in_user_can_delete_a_client()
    {
        // Login first user
        $user = $this->actingAsUser();

        // Generate client
        $client = App\Client::create([
            'email' => 'client@domain.com',
            'name'  => 'Test Client'
        ]);

        // Visit route
        $this->delete(route('client.destroy', [
            'client' => $client
        ]));

        // Verify redirected to correct page
        $this->followRedirects();
        $this->seePageIs(route('client.index'));

        // Verify client added to database
        $this->dontSeeInDatabase('clients', [
            'id' => $client->id
        ]);
    }
}
