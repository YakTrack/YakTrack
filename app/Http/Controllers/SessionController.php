<?php

namespace App\Http\Controllers;

use App\Models\Invoice;
use App\Models\Queries\IndexSessionQuery;
use App\Models\Session;
use App\Models\SessionCategory;
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
        if (!request()->has('per-page')) {
            request()->session()->reflash();

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
                        'date'                   => $date,
                        'sessions'               => $sessionsOnDay,
                        'totalDurationForHumans' => $sessionsOnDay->totalDurationForHumans(),
                    ];
                })->values(),
            'total'       => (int) $total = Session::count(),
            'perPage'     => (int) request('per-page'),
            'page'        => (int) $page,
            'lastPage'    => (int) request('per-page') ? ceil($total / request('per-page')) : 1,
        ]);
    }

    public function create()
    {
        return Inertia::render('Session/Edit', [
            'invoices'          => Invoice::all(),
            'sprints'           => Sprint::all(),
            'sessionCategories' => SessionCategory::all(),
            'tasks'             => Task::all(),
        ]);
    }

    public function store()
    {
        request()->validate([
            'started_at' => 'date|required',
            'ended_at'   => 'date|after:started_at',
            'sprint_id'  => 'exists:sprints,id',
            'task_id'    => 'exists:tasks,id',
            'invoice_id' => 'exists:invoices,id',
        ]);

        // If the new session has no end time, we want to stop any running sessions
        if (request('ended_at') == null) {
            Session::running()->get()->each(function ($session) {
                $session->stop();
            });
        }

        $session = Session::create([
            'started_at'            => $this->dateTimeFormatter->utcFormat(request('started_at')),
            'ended_at'              => request('ended_at') ? $this->dateTimeFormatter->utcFormat(request('ended_at')) : null,
            'task_id'               => request('task_id') ?: null,
            'invoice_id'            => request('invoice_id') ?: null,
            'sprint_id'             => request('sprint_id') ?: null,
            'session_category_id'   => request('session_category_id') ?: null,
            'comment'               => request('comment') ?: null,
			'is_billable'   		=> request('is_billable', 1),
        ]);

        return redirect()
            ->route('session.index')
            ->with('success', "Session $session->id created");
    }

    public function edit(Session $session)
    {
        return Inertia::render('Session/Edit', [
            'session'           => $session,
            'tasks'             => Task::with('project.client')->orderBy('id', 'desc')->get(),
            'invoices'          => Invoice::orderBy('id', 'desc')->get(),
            'sprints'           => Sprint::with('project.client')->orderBy('id', 'desc')->get(),
            'sessionCategories' => SessionCategory::all(),
        ]);
    }

    public function update(Session $session)
    {
        $session->update([
            'started_at'            => request('started_at') ? $this->dateTimeFormatter->utcFormat(request('started_at')) : null,
            'ended_at'              => request('ended_at') ? $this->dateTimeFormatter->utcFormat(request('ended_at')) : null,
            'task_id'               => request('task_id') ?: null,
            'invoice_id'            => request('invoice_id') ?: null,
            'sprint_id'             => request('sprint_id') ?: null,
            'session_category_id'   => request('session_category_id') ?: null,
            'comment'               => request('comment') ?: null,
			'is_billable' => request('is_billable') ?: 0,
        ]);

        return redirect()
            ->route('session.index')
            ->with('success', "Session $session->id updated");
    }

    public function start()
    {
        Session::running()->get()->each(function ($session) {
            $session->stop();
        });

        Session::create([
            'started_at'  => Carbon::now(),
            'is_billable' => 1,
        ]);

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
