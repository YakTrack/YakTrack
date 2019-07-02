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

        $tooLateForFilter = factory(Session::class)->create([
            'started_at' => '2019-01-03 00:00:00',
        ]);

        $this->actingAsUser();

        $response = $this->get(route('json.session.index', [
            'started-after'  => '2019-01-01 01:00:00',
            'started-before' => '2019-01-02 24:59:59',
        ]));

        $days = collect($response->decodeResponseJson()['days'])->keyBy(function ($day) {
            return $day['date'];
        })->map(function ($day) {
            return collect($day['sessions']);
        });

        $this->assertTrue($days->contains(function ($sessions, $date) use ($recentEnoughForFilter) {
            return $sessions->contains(function ($session) use ($recentEnoughForFilter) {
                return $session['id'] == $recentEnoughForFilter->id;
            });
        }));

        $this->assertFalse($days->contains(function ($sessions, $date) use ($tooLateForFilter) {
            return $sessions->contains(function ($session) use ($tooLateForFilter) {
                return $session['id'] == $tooLateForFilter->id;
            });
        }));

        $this->assertFalse($days->contains(function ($sessions, $date) use ($tooEarlyForFilter) {
            return $sessions->contains(function ($session) use ($tooEarlyForFilter) {
                return $session['id'] == $tooEarlyForFilter->id;
            });
        }));
    }
}
