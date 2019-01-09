<?php

use App\Models\Session;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Tests\TestCase;

class IndexSessionTest extends TestCase
{
    use DatabaseMigrations;

    /** @test */
    public function a_user_can_see_a_list_of_sessions()
    {
        $session = factory(Session::class)->create();

        $this->actingAsUser();

        $response = $this->get(route('session.index'));

        $response->assertViewIs('session.index');

        $response->assertSee($session->id);
    }

    /** @test */
    public function a_user_can_see_a_list_of_sessions_filtered_by_start_time()
    {
        $this->withoutExceptionHandling();

        Carbon::setTestNow(Carbon::parse('2019-01-08 00:00:00'));

        $tooEarlyForFilter = factory(Session::class)->create([
            'started_at' => '2019-01-01 00:00:00',
        ]);

        $recentEnoughForFilter = factory(Session::class)->create([
            'started_at' => '2019-01-02 00:00:00',
        ]);

        $this->actingAsUser();

        $response = $this->get(route('session.index', ['started_after' => '2019-01-01 01:00:00']));

        $response->assertViewIs('session.index');

        $this->assertTrue($response->viewData('days')->has('2019-01-02'));
        $this->assertFalse($response->viewData('days')->has('2019-01-01'));
    }
}
