<?php

namespace App\Http\Controllers;

use App\Session;
use App\Support\DateTimeFormatter;
use App\Support\DateIntervalFormatter;
use Carbon\CarbonInterval;
use App\Statistics\Sessions;

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
        $thisWeeksWorkSessions = Session::thisWeek()->finished()->get();

        return view('home', [
            'todaysWorkSessions' => $todaysWorkSessions = Session::today()->finished()->get(),
            'todaysTotal' => $this->dateIntervalFormatter->forHumans(CarbonInterval::seconds($todaysWorkSessions->sum(function ($session) {
                return $session->durationInSeconds;
            }))),
            'thisWeeksWorkSessions' => $this->dateTimeFormatter->daysThisWeek()->map(function ($day) {
                return $this->dateIntervalFormatter->forHumans($this->sessions->totalTimeOnDate($day));
            }),
        ]);
    }
}
