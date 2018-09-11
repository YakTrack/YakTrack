<?php

namespace App\Http\Controllers;

use App\Session;
use Carbon\Carbon;
use Illuminate\Http\Request;

class SessionController extends Controller
{
    public function index()
    {
        return view('session.index', [
            'days' => Session::orderBy('started_at', 'desc')
                ->get()
                ->groupBy(function ($session) {
                    return $session->startedAt->format('Y-m-d');
                })
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

    public function update(Session $session)
    {
        $session->update(request()->all());

        return response()->json($session);
    }

    public function start()
    {
        Session::running()->get()->each(function ($session) {
            $session->stop();
        });

        $session = Session::create(['started_at' => Carbon::now()]);

        return redirect(route('session.index'));
    }

    public function stop(Session $session)
    {
        $session->stop();

        return redirect(route('session.index'));
    }
}
