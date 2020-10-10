<?php

namespace Tests\Feature\DashboardTest;

use App\Models\Client;
use App\Models\Project;
use App\Models\Session;
use App\Models\Task;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DashboardTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function a_user_can_view_the_dashboard()
    {
        $client = factory(Client::class)->create();
        $project = factory(Project::class)->create([
            'client_id' => $client->id,
        ]);
        $task = factory(Task::class)->create([
            'project_id' => $project->id,
        ]);

        $this->withoutExceptionHandling();

        $session = factory(Session::class)->create([
            'started_at' => '2018-01-01 12:00:00',
            'ended_at'   => '2018-01-01 13:00:00',
        ]);

        $session = factory(Session::class)->create([
            'task_id'    => $task->id,
            'started_at' => '2018-01-01 13:00:00',
            'ended_at'   => null,
        ]);

        Carbon::setTestNow(Carbon::parse('2018-01-02'));

        $this->actingAsUser();

        $response = $this->get(route('home'));

        $response->assertSuccessful();

        $response->assertSee('Monday 1st Jan 2018');

        $response->assertPropValues([
            'currentlyWorking'      => true,
            'currentClientName'     => $client->name,
            'thisWeeksTotal'        => ($thisWeeksSessions = Session::thisWeek()->get())->totalDurationForHumans(),
        ]);

        Carbon::setTestNow();
    }
}
