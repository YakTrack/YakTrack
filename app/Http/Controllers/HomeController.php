<?php

namespace App\Http\Controllers;

use App\Models\Client;
use App\Models\Session;
use App\Statistics\Sessions;
use App\Support\DateIntervalFormatter;
use App\Support\DateTimeFormatter;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     */
    public function __construct(DateTimeFormatter $dateTimeFormatter, DateIntervalFormatter $dateIntervalFormatter, Sessions $sessions)
    {
        $this->middleware('auth');

        $this->dateTimeFormatter     = $dateTimeFormatter;
        $this->dateIntervalFormatter = $dateIntervalFormatter;
        $this->sessions              = $sessions;
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $noClient = (object) [
            'name'             => 'No Client',
            'sessionsThisWeek' => $noClientSessions = Session::thisWeek()
                ->get()
                ->filter(function ($session) {
                    return $session->hasNoClient();
                })->values(),
            'openSprints' => [],
        ];

        $clients = Client::with('projects.tasks.sessions')
            ->get()
            ->filter(function ($client) {
                return $client->sessionsThisWeek->count() > 0;
            });

        return view('home', [
            'thisWeeksWorkSessions' => $this->sessions->thisWeeksWorkSessions(),
            'thisWeeksTotal'        => ($thisWeeksSessions = Session::thisWeek()->get())->totalDurationForHumans(),
            'clients'               => $noClientSessions->count() > 0 ? $clients->push($noClient) : $clients,
            'currentlyWorking'      => $currentlyWorking = $this->sessions->currentlyWorking(),
            'currentSession'        => $currentSession = $this->sessions->currentSession(),
            'totalSecondsThisWeek'  => $thisWeeksSessions->totalDurationInSeconds(),
            'currentClientName'     => $currentlyWorking ? $currentSession->getClient()->name ?? 'No Client' : null,
        ]);
    }
}
