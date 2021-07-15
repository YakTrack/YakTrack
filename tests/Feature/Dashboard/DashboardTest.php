<?php

namespace Tests\Feature\DashboardTest;

use App\Models\Client;
use App\Models\Project;
use App\Models\Session;
use App\Models\Sprint;
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
        $this->withoutExceptionHandling();
        $this->usingTestDisplayTimeZone();

        $client = factory(Client::class)->create();
        $project = factory(Project::class)->create([
            'client_id' => $client->id,
        ]);
        $clientZeroSprint = factory(Sprint::class)->create([
            'project_id' => $project->id,
            'is_open'    => 1,
        ]);

        $task = factory(Task::class)->create([
            'project_id' => $project->id,
        ]);

        $uncategorisedSession = factory(Session::class)->state('billable')->create([
            'started_at' => '2018-01-01 12:00:00',
            'ended_at'   => '2018-01-01 13:00:00',
        ]);

        $session = factory(Session::class)->create([
            'task_id'    => $task->id,
            'sprint_id'  => $clientZeroSprint->id,
            'started_at' => '2018-01-01 13:00:00',
            'ended_at'   => null,
        ]);

        Carbon::setTestNow(Carbon::parse('2018-01-02'));

        $this->actingAsUser();

        $response = $this->get(route('home'));

        $response->assertSuccessful();

        $response->assertSee('Monday 1st Jan 2018');

        $this->assertArrayMatches([
            'clients' => [
                [
                    'id'        => $client->id,
                    'name'      => $client->name,
                    'this_week' => [
                        'billable' => [
                            'actual' => $client->sessionsThisWeek->whereBillable()->totalDurationInSeconds(),
                            'target' => 0,
                        ],
                        'not_billable' => [
                            'actual' => $client->sessionsThisWeek->whereNotBillable()->totalDurationInSeconds(),
                            'target' => 0,
                        ],
                    ],
                    'open_sprints' => [
                        [
                            'id'        => $clientZeroSprint->id,
                            'name'      => $clientZeroSprint->name,
                            'this_week' => [
                                'billable' => [
                                    'actual' => $clientZeroSprint->sessions()
                                        ->whereThisWeek()
                                        ->whereBillable()
                                        ->get()
                                        ->totalDurationInSeconds(),
                                    'target' => 0,
                                ],
                                'not_billable' => [
                                    'actual' => $clientZeroSprint->sessions()
                                        ->whereThisWeek()
                                        ->whereNotBillable()
                                        ->get()
                                        ->totalDurationInSeconds(),
                                    'target' => 0,
                                ],
                            ],
                        ],
                    ],
                ],
            ],
            'this_week' => [
                'billable' => [
                    'actual' => $uncategorisedSession->durationInSeconds,
                    'target' => 0,
                ],
                'not_billable' => [
                    'actual' => Session::whereThisWeek()->whereNotBillable()->get()->totalDurationInSeconds(),
                    'target' => 0,
                ],
                'days' => [
                    'monday' => [
                        'date'     => '2018-01-01',
                        'is_today' => false,
                        'billable' => [
                            'actual'    => $uncategorisedSession->durationInSeconds,
                            'target'    => 0,
                            'is_active' => false,
                        ],
                        'not_billable' => [
                            'actual' => 0,
                            'target' => 0,
                        ],
                    ],
                ],
            ],
        ], $response->props());

        $response->assertPropValues([
            'currentlyWorking'      => true,
            'currentClientName'     => $client->name,
            'thisWeeksTotal'        => ($thisWeeksSessions = Session::thisWeek()->get())->totalDurationForHumans(),
        ]);

        Carbon::setTestNow();
    }
}
