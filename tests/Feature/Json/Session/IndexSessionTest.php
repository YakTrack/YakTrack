<?php

namespace Tests\Feature\Json\Session;

use App\Models\Session;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class IndexSessionTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function a_user_can_see_a_list_of_sessions_filtered_by_start_time()
    {
        $this->withoutExceptionHandling();
        $this->usingTestDisplayTimezone();

        Carbon::setTestNow(Carbon::parse('2019-01-08 00:00:00'));

        $tooEarlyForFilter = factory(Session::class)->create([
            'started_at' => '2019-01-01 00:00:00',
        ]);

        $recentEnoughForFilter = factory(Session::class)->create([
            'started_at' => '2019-01-02 00:00:00',
        ]);

        $this->actingAsUser();

        $response = $this->get(route('json.session.index', ['started_after' => '2019-01-01 01:00:00']));
        $response->assertJsonFragment([
            'date'     => '2019-01-02',
        ]);
    }
}
