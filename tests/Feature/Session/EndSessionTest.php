<?php

namespace Tests\Feature\Session;

use App\Models\Session;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class EndSessionTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function a_user_can_end_a_session_with_a_get_request()
    {
        Carbon::setTestNow(Carbon::parse('2018-01-01 00:10:00'));

        $this->withoutExceptionHandling();

        $session = factory(Session::class)->create([
            'started_at' => '2018-01-01 00:00:00',
            'ended_at'   => null,
        ]);

        $this->actingAsUser();

        $response = $this->get(route('session.stop'));

        $response->assertRedirect(route('session.index'));

        $this->assertDatabaseHas('sessions', [
            'started_at' => '2018-01-01 00:00:00',
            'ended_at'   => '2018-01-01 00:10:00',
        ]);

        Carbon::setTestNow();
    }
}
