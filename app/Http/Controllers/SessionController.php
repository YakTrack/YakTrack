<?php

namespace App\Http\Controllers;

use App\Http\Requests\SplitSessionRequest;
use App\Models\Invoice;
use App\Models\Queries\IndexSessionQuery;
use App\Models\Session;
use App\Models\SessionCategory;
use App\Models\Sprint;
use App\Models\Task;
use App\Models\ThirdPartyApplication;
use App\Services\SessionSplitter;
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
            'sprints'                => Sprint::with('projects.client')->orderBy('id', 'desc')->get(),
            'tasks'                  => $this->sessionFormTasks(),
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
            'invoices'          => Invoice::query()->select(['id', 'number'])->orderByDesc('id')->get(),
            'sprints'           => Sprint::query()
                ->select(['id', 'name'])
                ->with(['projects' => function ($query) {
                    $query->select(['projects.id', 'projects.name']);
                }])
                ->orderByDesc('id')
                ->get(),
            'sessionCategories' => SessionCategory::query()->select(['id', 'name'])->get(),
            'tasks'             => $this->sessionFormTasks(),
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
            'tasks'             => $this->sessionFormTasks($session),
            'invoices'          => Invoice::query()->select(['id', 'number'])->orderByDesc('id')->get(),
            'sprints'           => Sprint::query()
                ->select(['id', 'name'])
                ->with(['projects' => function ($query) {
                    $query->select(['projects.id', 'projects.name']);
                }])
                ->orderByDesc('id')
                ->get(),
            'sessionCategories' => SessionCategory::query()->select(['id', 'name'])->get(),
        ]);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Collection<int, Task>
     */
    private function sessionFormTasks(?Session $session = null): \Illuminate\Database\Eloquent\Collection
    {
        return Task::query()
            ->select(['id', 'name', 'project_id', 'status_id'])
            ->with([
                'project:id,name,client_id',
                'project.client:id,name',
                'taskStatus:id,is_closed',
            ])
            ->where(function ($query) use ($session) {
                $query->whereHas('project', function ($projectQuery) {
                    $projectQuery->whereNull('archived_at');
                })->where(function ($taskQuery) {
                    $taskQuery->whereNull('status_id')
                        ->orWhereHas('taskStatus', function ($statusQuery) {
                            $statusQuery->where('is_closed', false);
                        });
                });

                if ($session?->task_id) {
                    $query->orWhere('id', $session->task_id);
                }
            })
            ->orderByDesc('id')
            ->get();
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
            $projectIds = $session->sprint->projects()->pluck('id');
            $openSprints = Sprint::open()->whereHas('projects', fn ($q) => $q->whereIn('id', $projectIds))
                ->orderBy('id', 'desc')->get();
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

    public function split(SplitSessionRequest $request, Session $session, SessionSplitter $sessionSplitter): RedirectResponse
    {
        if ($session->isRunning()) {
            return redirect()
                ->route('session.index')
                ->with('error', 'Cannot split a running session. Please stop the session first.');
        }

        if ($request->has('segments')) {
            /** @var array<int, array{started_at: string, ended_at: string, sprint_id?: int|null, task_id?: int|null}> $segments */
            $segments = $request->input('segments');
            $result = $sessionSplitter->split($session, $segments);
            $createdCount = count($result['created']);

            return redirect()
                ->route('session.index')
                ->with('success', "Session {$session->id} split into ".($createdCount + 1).' sessions.');
        }

        $result = $sessionSplitter->split($session, [
            [
                'started_at' => $session->localStartedAt->format('Y-m-d H:i:s'),
                'ended_at'   => $request->input('split_time'),
            ],
            [
                'started_at' => $request->input('split_time'),
                'ended_at'   => $session->localEndedAt->format('Y-m-d H:i:s'),
            ],
        ]);

        $newSession = $result['created'][0];

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
