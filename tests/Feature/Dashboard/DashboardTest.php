<?php

use App\Models\Client;
use App\Models\Project;
use App\Models\Session;
use App\Models\Sprint;
use App\Models\Target;
use App\Models\Task;
use Carbon\Carbon;

it('can view the dashboard', function () {
    $this->withoutExceptionHandling();
    $this->usingTestDisplayTimeZone();

    $client = Client::factory()->create();
    $project = Project::factory()->create([
        'client_id' => $client->id,
    ]);
    $clientZeroSprint = Sprint::factory()->create([
        'project_id' => $project->id,
        'is_open'    => 1,
    ]);

    $task = Task::factory()->create([
        'project_id' => $project->id,
    ]);

    $uncategorisedSession = Session::factory()->billable()->create([
        'started_at' => '2018-01-01 12:00:00',
        'ended_at'   => '2018-01-01 13:00:00',
    ]);

    $session = Session::factory()->create([
        'task_id'    => $task->id,
        'sprint_id'  => $clientZeroSprint->id,
        'started_at' => '2018-01-01 01:00:00',
        'ended_at'   => null,
    ]);

    $mondayBillableTarget = Target::factory()
        ->forDate()->inHours()
        ->create([
            'billable_only' => 1,
            'starts_at'     => '2018-01-01 00:00:00',
            'value'         => 5,
        ]);

    $mondayNonBillableTarget = Target::factory()
        ->forDate()->inHours()
        ->create([
            'billable_only' => 0,
            'starts_at'     => '2018-01-01 00:00:00',
            'value'         => 1,
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
                'target' => $mondayBillableTarget->valueInSeconds(),
            ],
            'not_billable' => [
                'actual' => Session::whereThisWeek()->whereNotBillable()->get()->totalDurationInSeconds(),
                'target' => $mondayNonBillableTarget->valueInSeconds(),
            ],
            'days' => [
                'monday' => [
                    'date'     => '2018-01-01',
                    'is_today' => false,
                    'billable' => [
                        'actual'    => $uncategorisedSession->durationInSeconds,
                        'target'    => $mondayBillableTarget->valueInSeconds(),
                        'is_active' => false,
                    ],
                    'not_billable' => [
                        'actual' => $session->durationInSeconds,
                        'target' => $mondayNonBillableTarget->valueInSeconds(),
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
});