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
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;

class SessionController extends Controller
{
    private IndexSessionQuery $indexSessionQuery;
    private DateTimeFormatter $dateTimeFormatter;

    public function __construct(DateTimeFormatter $dateTimeFormatter, IndexSessionQuery $indexSessionQuery)
    {
        $this->indexSessionQuery = $indexSessionQuery;
        $this->dateTimeFormatter = $dateTimeFormatter;
    }

    public function index(): RedirectResponse|Response
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

        /** @var \Illuminate\Support\Collection<string, \Illuminate\Support\Collection<int, \App\Models\Session>> $groupedSessions */
        $groupedSessions = $sessions->groupBy(function ($session) {
            return $session->localStartedAt->format('Y-m-d');
        });

        /** @var \Illuminate\Support\Collection<int, array{date: string, sessions: \Illuminate\Support\Collection<int, \App\Models\Session>, totalDurationForHumans: string}> $days */
        $days = $groupedSessions->map(function (\Illuminate\Support\Collection $sessionsOnDay, string $date): array {
            return [
                'date'                   => $date,
                'sessions'               => $sessionsOnDay,
                'totalDurationForHumans' => $sessionsOnDay->totalDurationForHumans(),
            ];
        })->values();

        return Inertia::render('Session/Index', [
            'invoices'               => Invoice::all(),
            'thirdPartyApplications' => ThirdPartyApplication::all(),
            'sprints'                => Sprint::with('project.client')->orderBy('id', 'desc')->get(),
            'days'                   => $days,
            'total'                  => (int) $total = Session::count(),
            'perPage'                => (int) request('per-page'),
            'page'                   => (int) $page,
            'lastPage'               => (int) request('per-page') ? ceil($total / request('per-page')) : 1,
        ]);
    }

    public function create(): Response
    {
        return Inertia::render('Session/Edit', [
            'invoices'          => Invoice::all(),
            'sprints'           => Sprint::all(),
            'sessionCategories' => SessionCategory::all(),
            'tasks'             => Task::all(),
        ]);
    }

    public function store(): RedirectResponse
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
            'is_billable'   		      => request('is_billable', 1),
        ]);

        return redirect()
            ->route('session.index')
            ->with('success', "Session $session->id created");
    }

    public function edit(Session $session): Response
    {
        return Inertia::render('Session/Edit', [
            'session'           => $session,
            'tasks'             => Task::with('project.client')->orderBy('id', 'desc')->get(),
            'invoices'          => Invoice::orderBy('id', 'desc')->get(),
            'sprints'           => Sprint::with('project.client')->orderBy('id', 'desc')->get(),
            'sessionCategories' => SessionCategory::all(),
        ]);
    }

    public function update(Session $session): RedirectResponse
    {
        $session->update([
            'started_at'            => request('started_at') ? $this->dateTimeFormatter->utcFormat(request('started_at')) : null,
            'ended_at'              => request('ended_at') ? $this->dateTimeFormatter->utcFormat(request('ended_at')) : null,
            'task_id'               => request('task_id') ?: null,
            'invoice_id'            => request('invoice_id') ?: null,
            'sprint_id'             => request('sprint_id') ?: null,
            'session_category_id'   => request('session_category_id') ?: null,
            'comment'               => request('comment') ?: null,
            'is_billable'           => request('is_billable') ?: 0,
        ]);

        return redirect()
            ->route('session.index')
            ->with('success', "Session $session->id updated");
    }

    public function start(): RedirectResponse
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

    public function stop(): RedirectResponse
    {
        Session::running()->get()->each(function ($session) {
            $session->stop();
        });

        return redirect(route('session.index'));
    }

    public function continue(Session $session): RedirectResponse
    {
        Session::running()->get()->each(function ($session) {
            $session->stop();
        });

        if ($session->sprint_id) {
            $openSprints = $session->sprint->project->sprints()->open()->orderBy('id', 'desc')->get();
            $newSprintId = $openSprints->isEmpty()
                ? $session->sprint_id
                : $openSprints->first()->id;
        } else {
            $newSprintId = null;
        }

        Session::create([
            'started_at'            => Carbon::now(),
            'is_billable'           => $session->is_billable ?? 1,
            'task_id'               => $session->task_id,
            'sprint_id'             => $newSprintId,
            'session_category_id'   => $session->session_category_id,
        ]);

        return redirect(route('session.index'));
    }

    public function split(Session $session): RedirectResponse
    {
        // Ensure we're not trying to split a running session
        if ($session->isRunning()) {
            return redirect()
                ->route('session.index')
                ->with('error', 'Cannot split a running session. Please stop the session first.');
        }

        request()->validate([
            'split_time' => [
                'required',
                'date',
                'after:'.$session->localStartedAt->format('Y-m-d H:i:s'),
                'before:'.$session->localEndedAt->format('Y-m-d H:i:s'),
            ],
        ]);

        $splitTime = $this->dateTimeFormatter->utcFormat(request('split_time'));

        // Create the new session (second half)
        $newSession = Session::create([
            'started_at'            => $splitTime,
            'ended_at'              => $session->ended_at,
            'task_id'               => $session->task_id,
            'invoice_id'            => $session->invoice_id,
            'sprint_id'             => $session->sprint_id,
            'session_category_id'   => $session->session_category_id,
            'comment'               => $session->comment,
            'is_billable'           => $session->is_billable,
        ]);

        // Update the original session (first half)
        $session->update([
            'ended_at' => $splitTime,
        ]);

        return redirect()
            ->route('session.index')
            ->with('success', "Session split successfully. Session {$session->id} ends at split time, new session {$newSession->id} starts from split time.");
    }

    public function destroy(Session $session): RedirectResponse
    {
        $session->delete();

        return redirect()->route('session.index');
    }
}
