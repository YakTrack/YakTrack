<?php

namespace App\Http\Controllers;

use App\Models\Client;
use App\Models\Session;
use App\Models\Target;
use App\Statistics\Sessions;
use App\Support\DateTimeFormatter;
use Inertia\Inertia;
use Inertia\Response;

class HomeController extends Controller
{
    private DateTimeFormatter $dateTimeFormatter;
    private Sessions $sessions;

    /**
     * Create a new controller instance.
     */
    public function __construct(DateTimeFormatter $dateTimeFormatter, Sessions $sessions)
    {
        $this->middleware('auth');

        $this->dateTimeFormatter = $dateTimeFormatter;
        $this->sessions = $sessions;
    }

    /**
     * Show the application dashboard.
     */
    public function index(): Response
    {
        $currentSession = Session::whereIsRunning()->first();

        $noClientSessions = Session::thisWeek()
            ->get()
            ->filter(function ($session) {
                return $session->hasNoClient();
            });

        $noClient = [
            'id'        => 0,
            'name'      => 'No Client',
            'this_week' => [
                'billable' => [
                    'actual' => $noClientSessions->filter(fn($session) => $session->is_billable)->sum('durationInSeconds'),
                    'target' => 0,
                ],
                'not_billable' => [
                    'actual' => $noClientSessions->filter(fn($session) => !$session->is_billable)->sum('durationInSeconds'),
                    'target' => 0,
                ],
            ],
            'open_sprints' => [],
        ];

        $clients = Client::with(['projects.tasks.sessions'])
            ->get()
            ->filter(function ($client) {
                $client->append(['openSprints', 'sessionsThisWeek']);

                return $client->sessionsThisWeek->count() > 0;
            })->map(function ($client) {
                return [
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
                    'open_sprints' => $client->openSprints->map(function ($sprint) {
                        return [
                            'id'        => $sprint->id,
                            'name'      => $sprint->name,
                            'this_week' => [
                                'billable' => [
                                    'actual' => $sprint->sessions->whereBillable()->whereThisWeek()->totalDurationInSeconds(),
                                    'target' => 0,
                                ],
                                'not_billable' => [
                                    'actual' => $sprint->sessions->whereNotBillable()->whereThisWeek()->totalDurationInSeconds(),
                                    'target' => 0,
                                ],
                            ],
                        ];
                    }),
                ];
            });

        return Inertia::render('Home', [
            'this_week' => [
                'billable' => [
                    'actual' => Session::whereThisWeek()->whereBillable()->get()->totalDurationInSeconds(),
                    'target' => Target::whereForThisWeek()->whereBillableOnly()->get()->totalValueInSeconds(),
                ],
                'not_billable' => [
                    'actual' => Session::whereThisWeek()->whereNotBillable()->get()->totalDurationInSeconds(),
                    'target' => Target::whereForThisWeek()->whereNotBillableOnly()->get()->totalValueInSeconds(),
                ],
                'days' => collect($this->dateTimeFormatter::DAYS_OF_WEEK)->mapWithKeys(function (string $day) use ($currentSession) {
                    $date = $this->dateTimeFormatter->dayThisWeek(strtolower($day));

                    return [
                        ($day = strtolower($day)) => [
                            'date'     => $date->format('Y-m-d'),
                            'is_today' => $date->isToday(),
                            'billable' => [
                                'actual'    => Session::whereOnDayThisWeek($day)->whereBillable()->get()->totalDurationInSeconds(),
                                'target'    => Target::whereForDate($date)->whereBillableOnly()->get()->totalValueInSeconds(),
                                'is_active' => $currentSession && $currentSession->is_billable && $date->isToday(),
                            ],
                            'not_billable' => [
                                'actual'    => Session::whereOnDayThisWeek($day)->whereNotBillable()->get()->totalDurationInSeconds(),
                                'target'    => Target::whereForDate($date)->whereNotBillableOnly()->get()->totalValueInSeconds(),
                                'is_active' => $currentSession && !$currentSession->is_billable && $date->isToday(),
                            ],
                        ],
                    ];
                })->toArray(),
            ],
            'thisWeeksTotal'                           => ($thisWeeksSessions = Session::thisWeek()->get())->totalDurationForHumans(),
            'totalSecondsRemainingForTargetsThisWeek'  => Target::whereForThisWeek()->get()->totalValueInSeconds() - $thisWeeksSessions->totalDurationInSeconds(),
            'clients'                                  => $noClientSessions->count() > 0 ? $clients->push($noClient)->values() : $clients,
            'currentlyWorking'                         => $currentlyWorking = $this->sessions->currentlyWorking(),
            'currentSession'                           => $currentSession = $this->sessions->currentSession(),
            'totalSecondsThisWeek'                     => $thisWeeksSessions->totalDurationInSeconds(),
            'currentClientName'                        => $currentlyWorking ? $currentSession->getClient()->name ?? 'No Client' : null,
        ]);
    }
}
