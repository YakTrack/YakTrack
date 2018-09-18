<?php

use App\Session;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\DatabaseMigrations;

class DashboardTest extends BrowserKitTestCase
{
    use DatabaseMigrations;

    /** @test */
    public function a_user_can_view_the_dashboard()
    {
        $session = factory(Session::class)->create([
            'started_at' => '2018-01-01 12:00:00',
            'ended_at' => '2018-01-01 13:00:00',
        ]);

        Carbon::setTestNow(Carbon::parse('2018-01-02'));

        $this->actingAsUser();

        $this->visit(route('home'));

        $this->seePageIs(route('home'));

        Carbon::setTestNow();
    }
}
