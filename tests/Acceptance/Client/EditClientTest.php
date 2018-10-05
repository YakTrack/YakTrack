<?php

use Illuminate\Foundation\Testing\DatabaseMigrations;

class EditClientTest extends BrowserKitTestCase
{
    use DatabaseMigrations;

    /** @test */
    public function a_logged_in_user_can_edit_a_client()
    {
        // Create client
        $client = App\Client::create([
            'email' => 'client@domain.com',
            'name'  => 'Test Client',
        ]);

        // Login user
        $user = $this->actingAsUser();

        // Visit edit client page
        $this->visit(route('client.edit', ['client' => $client]));

        // Verify correct page has loaded
        $this->seePageIs(route('client.edit', ['client' => $client]));

        // Add new client information and press update
        $this->type('updatedclient@domain.com', 'email')
            ->type('Updated Client', 'name')
            ->press('Update');

        // Verify redirected to correct page
        $this->seePageIs(route('client.index'));

        // Verify databse updated
        $this->seeInDatabase('clients', [
            'name'  => 'Updated Client',
            'email' => 'updatedclient@domain.com',
        ]);
    }
}
