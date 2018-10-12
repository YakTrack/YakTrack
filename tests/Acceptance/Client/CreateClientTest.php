<?php

use Illuminate\Foundation\Testing\DatabaseMigrations;

class CreateClientTest extends BrowserKitTestCase
{
    use DatabaseMigrations;

    /** @test */
    public function a_logged_in_user_can_create_a_client()
    {
        // Login first user
        $user = $this->actingAsUser();

        // Visit page
        $this->visit(route('client.create'));

        // Verify correct page loads
        $this->seePageIs(route('client.create'));

        // Fill in form an submit
        $this->type('Test Client', 'name')
            ->type('client@domain.com', 'email')
            ->press('Create');

        // Verify redirected to correct page
        $this->seePageIs(route('client.index'));

        // Verify client added to database
        $this->seeInDatabase('clients', [
            'name'  => 'Test Client',
            'email' => 'client@domain.com',
        ]);
    }
}
