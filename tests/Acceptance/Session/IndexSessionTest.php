<?php

use App\Session;
use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class IndexSessionTest extends BrowserKitTestCase
{
    use DatabaseMigrations;

    /** @test */
    public function a_user_can_see_a_list_of_sessions()
    {
        $session = factory(Session::class)->create();

        $this->actingAsUser();

        $this->visit(route('session.index'));

        $this->seePageIs(route('session.index'));

        $this->see($session->id);
    }
}
