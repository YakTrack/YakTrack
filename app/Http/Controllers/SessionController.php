<?php

namespace App\Http\Controllers;

use App\Models\Invoice;
use App\Models\Session;
use App\Models\Task;
use App\Models\ThirdPartyApplication;
use App\Support\DateTimeFormatter;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;

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
                ->with(['task.project.client', 'invoice', 'thirdPartyApplicationSessions'])
                ->get()
                ->groupBy(function ($session) {
                    return $session->localStartedAt->format('Y-m-d');
                }),
            'thirdPartyApplications' => ThirdPartyApplication::all(),
        ]);
    }

    public function filter(Request $request)
    {
        $query = Session::orderBy('started_at', 'desc')
            ->with(['task.project.client', 'invoice', 'thirdPartyApplicationSessions']);

        if ($request->has(['start', 'end'])) {
            $query = $query->where('started_at', '>', Carbon::parse($request->start))->where('ended_at', '<', Carbon::parse($request->end));
        }

        return $query->get()
            ->groupBy(function ($session) {
                return $session->localStartedAt->format('Y-m-d');
            });
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
            'session'  => $session,
            'tasks'    => Task::all(),
            'invoices' => Invoice::all(),
        ]);
    }

    public function showEdit($id)
    {
        $session = Session::where('id', $id)->first();

        return Redirect::route('session.edit', ['session' => $session]);
    }

    public function update(Session $session)
    {
        $session->update([
            'started_at' => request('started_at') ? $this->dateTimeFormatter->utcFormat(request('started_at')) : null,
            'ended_at'   => request('ended_at') ? $this->dateTimeFormatter->utcFormat(request('ended_at')) : null,
            'task_id'    => request('task_id') ?: null,
            'invoice_id' => request('invoice_id') ?: null,
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

    public function destroy(Session $session)
    {
        $session->delete();

        return redirect()->route('session.index');
    }

    public function destroyById($id)
    {
        Session::where('id', $id)->first()->delete();

        return 200;
    }
}
