<?php

namespace App\Http\Controllers;

use App\Models\Session;
use App\Models\Task;
use App\Models\ThirdPartyApplication;
use App\Support\DateTimeFormatter;
use Carbon\Carbon;
use Illuminate\Http\Request;

class SessionController extends Controller
{
    public function __construct(DateTimeFormatter $dateTimeFormatter)
    {
        $this->dateTimeFormatter = $dateTimeFormatter;
    }

    public function index()
    {
        return view('session.index', [
            'days' => Session::orderBy('started_at', 'desc')
                ->get()
                ->groupBy(function ($session) {
                    return $session->localStartedAt->format('Y-m-d');
                }),
            'thirdPartyApplications' => ThirdPartyApplication::all(),
        ]);
    }

    public function store()
    {
        Session::running()->get()->each(function ($session) {
            $session->stop();
        });

        $session = Session::create([
            'started_at' => request('started_at') ?? Carbon::now()->format('Y-m-d H:i:s'),
        ]);

        return response()->json($session);
    }

    public function edit(Session $session)
    {
        return view('session.edit', [
            'session' => $session,
            'tasks'   => Task::all(),
        ]);
    }

    public function update(Session $session)
    {
        $session->update([
            'started_at' => request('started_at') ? $this->dateTimeFormatter->utcFormat(request('started_at')) : null,
            'ended_at'   => request('ended_at') ? $this->dateTimeFormatter->utcFormat(request('ended_at')) : null,
            'task_id'    => request('task_id') ?: null,
        ]);

        return request()->expectsJson() ? response()->json($session) : redirect()->route('session.index');
    }

    public function start()
    {
        Session::running()->get()->each(function ($session) {
            $session->stop();
        });

        $session = Session::create(['started_at' => Carbon::now()]);

        return redirect(route('session.index'));
    }

    public function stop()
    {
        Session::running()->get()->each(function ($session) {
            $session->stop();
        });

        return redirect(route('session.index'));
    }
}
