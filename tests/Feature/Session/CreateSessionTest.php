<?php
/**
 * Created by PhpStorm.
 * User: dominiksecka
 * Date: 2018-12-14
 * Time: 22:28.
 */

namespace Tests\Feature\Session;

use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CreateSessionTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function a_user_can_create_a_session()
    {
        Carbon::setTestNow(Carbon::parse('2018-01-01 00:00:00'));

        $this->actingAsUser();

        $response = $this->get(route('session.start'));

        $response->assertRedirect(route('session.index'));

        $this->assertDatabaseHas('sessions', [
            'started_at' => '2018-01-01 00:00:00',
        ]);

        Carbon::setTestNow();
    }

    /** @test */
    public function a_user_can_store_session_by_post()
    {

        Carbon::setTestNow(Carbon::parse('2018-01-01 00:00:00'));

        $this->actingAsUser();

        $response = $this->get(route('session.start'));

        $response->assertRedirect(route('session.index'));

        $this->assertDatabaseHas('sessions', [
            'ended_at' => null,
        ]);

        $response = $this->post(route('session.store'), ['started_at' => '2018-02-01 00:00:00']);

        $response->assertSuccessful();

        $this->assertDatabaseHas('sessions', [
            'ended_at' => '2018-01-01 00:00:00',
        ]);

        $this->assertDatabaseHas('sessions', [
            'started_at' => '2018-02-01 00:00:00',
        ]);

        Carbon::setTestNow();
    }
}