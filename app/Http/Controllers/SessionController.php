<?php

namespace App\Http\Controllers;

use App\Models\Invoice;
use App\Models\Queries\IndexSessionQuery;
use App\Models\Session;
use App\Models\Sprint;
use App\Models\Task;
use App\Models\ThirdPartyApplication;
use App\Support\DateTimeFormatter;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Inertia\Inertia;

class SessionController extends Controller
{
    public function __construct(DateTimeFormatter $dateTimeFormatter, IndexSessionQuery $indexSessionQuery)
    {
        $this->indexSessionQuery = $indexSessionQuery;
        $this->dateTimeFormatter = $dateTimeFormatter;
    }

    public function index()
    {
        if (is_null(request('per-page'))) {
            return redirect()->route('session.index', array_merge(request()->query(), ['per-page' => 100]));
        }
        $page = request('page') ?? 1;

        $sessions = $this->indexSessionQuery
            ->offset(request('per-page') * $page)
            ->paginate(request('per-page'))
            ->execute();

        return Inertia::render('Session/Index', [
            'invoices'               => Invoice::all(),
            'thirdPartyApplications' => ThirdPartyApplication::all(),
            'days'                   => $sessions
                ->groupBy(function ($session) {
                    return $session->localStartedAt->format('Y-m-d');
                })->map(function ($sessionsOnDay, $date) {
                    return [
                        'date'     => $date,
                        'sessions' => $sessionsOnDay,
                    ];
                })->values(),
            'total'       => (int) $total = Session::count(),
            'perPage'     => (int) request('per-page'),
            'page'        => (int) $page,
            'lastPage'    => (int) request('per-page') ? ceil($total / request('per-page')) : 1,
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
            'session'  => $session,
            'tasks'    => Task::orderBy('id', 'desc')->get(),
            'invoices' => Invoice::orderBy('id', 'desc')->get(),
            'sprints'  => Sprint::with('project.client')->orderBy('id', 'desc')->get(),
        ]);
    }

    public function update(Session $session)
    {
        $session->update([
            'started_at' => request('started_at') ? $this->dateTimeFormatter->utcFormat(request('started_at')) : null,
            'ended_at'   => request('ended_at') ? $this->dateTimeFormatter->utcFormat(request('ended_at')) : null,
            'task_id'    => request('task_id') ?: null,
            'invoice_id' => request('invoice_id') ?: null,
            'sprint_id'  => request('sprint_id') ?: null,
        ]);

        return redirect()->route('session.index');
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
}
