<?php
/**
 * Created by PhpStorm.
 * User: dominiksecka
 * Date: 2018-12-14
 * Time: 22:22
 */

namespace Tests\Feature\Session;


use App\Models\Session;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class FilterSessionTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function a_user_should_filter_expected_sessions_by_time_interval()
    {

        factory(Session::class)->create([
            'started_at' => '2018-01-01 00:00:00',
            'ended_at'   => '2018-01-01 00:10:00',
        ]);

        factory(Session::class)->create([
            'started_at' => '2018-02-01 00:00:00',
            'ended_at'   => '2018-02-01 00:25:00',
        ]);

        factory(Session::class)->create([
            'started_at' => '2018-02-01 00:35:00',
            'ended_at'   => '2018-02-01 00:46:00',
        ]);

        $this->actingAsUser();

        $response = $this->post('session/filter', [
            'start' => '2018-01-01 00:00:00',
            'end' => '2018-03-01 00:46:00',
        ]);

        $response->assertSuccessful();

        $res_array = $response->decodeResponseJson();

        $this->assertTrue(count($res_array['2018-02-01']) == 2);
    }

    /** @test */
    public function a_user_should_filter_no_sessions_by_time_interval()
    {

        factory(Session::class)->create([
            'started_at' => '2018-01-01 00:00:00',
            'ended_at'   => '2018-01-01 00:10:00',
        ]);

        factory(Session::class)->create([
            'started_at' => '2018-02-01 00:00:00',
            'ended_at'   => '2018-02-01 00:25:00',
        ]);

        factory(Session::class)->create([
            'started_at' => '2018-02-01 00:35:00',
            'ended_at'   => '2018-02-01 00:46:00',
        ]);

        $this->actingAsUser();

        $response = $this->post('session/filter', [
            'start' => '2018-05-01 00:00:00',
            'end' => '2018-06-01 00:46:00',
        ]);

        $response->assertSuccessful();

        $res_array = $response->decodeResponseJson();

        $this->assertTrue(count($res_array) == 0);
    }

}