<?php

namespace App\Http\Controllers;

use App\Models\Client;
use App\Models\Session;
use App\Models\Target;
use App\Statistics\Sessions;
use App\Support\DateIntervalFormatter;
use App\Support\DateTimeFormatter;
use Inertia\Inertia;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     */
    public function __construct(DateTimeFormatter $dateTimeFormatter, DateIntervalFormatter $dateIntervalFormatter, Sessions $sessions)
    {
        $this->middleware('auth');

        $this->dateTimeFormatter = $dateTimeFormatter;
        $this->dateIntervalFormatter = $dateIntervalFormatter;
        $this->sessions = $sessions;
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $currentSession = Session::whereIsRunning()->first();

        $noClient = (object) [
            'name'             => 'No Client',
            'sessionsThisWeek' => $noClientSessions = Session::thisWeek()
                ->get()
                ->filter(function ($session) {
                    return $session->hasNoClient();
                })->values(),
            'openSprints' => [],
        ];

        $clients = Client::with(['projects.tasks.sessions'])
            ->get()
            ->filter(function ($client) {
                $client->append(['openSprints', 'sessionsThisWeek']);

                return $client->sessionsThisWeek->count() > 0;
            })->map(function ($client) {
                return [
                    'id' => $client->id,
                    'name' => $client->name,
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
                    'open_sprints' => $client->openSprints->map(function ($sprint) {
                        return [
                            'id' => $sprint->id,
                            'name' => $sprint->name,
                            'this_week' => [
                                'billable' => [
                                    'actual' => $sprint->sessions->whereBillable()->whereThisWeek()->totalDurationInSeconds(),
                                    'target' => 0,
                                ],
                                'not_billable' => [
                                    'actual' => $sprint->sessions->whereNotBillable()->whereThisWeek()->totalDurationInSeconds(),
                                    'target' => 0,
                                ],
                            ]
                        ];
                    }),
                ];
            });

        return Inertia::render('Home', [
            'this_week' => [
                'billable' => [
                    'actual' => Session::whereThisWeek()->whereBillable()->get()->totalDurationInSeconds(),
                    'target' => 0,
                ],
                'not_billable' => [
                    'actual' => Session::whereThisWeek()->whereNotBillable()->get()->totalDurationInSeconds(),
                    'target' => 0,
                ],
                'days' => collect($this->dateTimeFormatter::DAYS_OF_WEEK)->mapWithKeys(function ($day) use ($currentSession) {

                    $date = $this->dateTimeFormatter->dayThisWeek(strtolower($day));

                    return [
                        ($day = strtolower($day)) => [
                            'date' => $date->format('Y-m-d'),
                            'is_today' => $date->isToday(),
                            'billable' => [
                                'actual' => Session::whereOnDayThisWeek($day)->whereBillable()->get()->totalDurationInSeconds(),
                                'target' => 0,
                                'is_active' => $currentSession && $currentSession->is_billable && $date->isToday(),
                            ],
                            'not_billable' => [
                                'actual' => Session::whereOnDayThisWeek($day)->whereNotBillable()->get()->totalDurationInSeconds(),
                                'target' => 0,
                                'is_active' => $currentSession && !$currentSession->is_billable && $date->isToday(),
                            ],
                        ],
                    ];
                })->toArray(),
            ],
            'thisWeeksTotal'        => ($thisWeeksSessions = Session::thisWeek()->get())->totalDurationForHumans(),
            'totalSecondsRemainingForTargetsThisWeek'  => Target::whereForThisWeek()->get()->totalValueInSeconds() - $thisWeeksSessions->totalDurationInSeconds(),
            'clients'               => $noClientSessions->count() > 0 ? $clients->push($noClient) : $clients,
            'currentlyWorking'      => $currentlyWorking = $this->sessions->currentlyWorking(),
            'currentSession'        => $currentSession = $this->sessions->currentSession(),
            'totalSecondsThisWeek'  => $thisWeeksSessions->totalDurationInSeconds(),
            'currentClientName'     => $currentlyWorking ? $currentSession->getClient()->name ?? 'No Client' : null,
        ]);
    }
}
