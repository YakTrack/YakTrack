<?php

namespace Tests\Feature\Session;

use App\Models\Session;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class DeleteSessionTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function a_user_can_delete_a_session()
    {
        $session = factory(Session::class)->create();

        $this->actingAsUser();

        $response = $this->delete(route('session.destroy', ['session' => $session]));

        $response->assertRedirect(route('session.index'));

        $this->assertDatabaseMissing('sessions', [
            'id' => $session->id,
        ]);
    }
}
