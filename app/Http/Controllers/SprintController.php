<?php

namespace App\Http\Controllers;

use App\Http\Requests\SprintIndexRequest;
use App\Models\Project;
use App\Models\Sprint;
use App\Support\DateIntervalFormatter;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;
use Inertia\Response;

class SprintController extends Controller
{
    /**
     * Display a list of sprints.
     */
    public function index(SprintIndexRequest $request): Response
    {
        $state = $request->tableState();
        $direction = $state['direction'];

        $query = Sprint::query()
            ->select('sprints.*')
            ->forFocusedClient($request->user()->focusedClientId());

        if ($state['q'] !== '') {
            $term = '%'.addcslashes($state['q'], '%_\\').'%';
            $query->where(function ($q) use ($term): void {
                $q->where('sprints.name', 'like', $term)
                    ->orWhereHas('projects', fn ($p) => $p->where('name', 'like', $term));
            });
        }

        if ($state['project_id'] !== null) {
            $query->whereHas('projects', fn ($p) => $p->whereKey($state['project_id']));
        }

        match ($state['lifecycle']) {
            'open'   => $query->where('sprints.is_open', 1),
            'closed' => $query->where('sprints.is_open', 0),
            default  => null,
        };

        $minProjectSql = $this->minProjectNameSubquerySql();
        $durationSql = $this->durationSumSubquerySql();

        match ($state['sort']) {
            'name'     => $query->orderBy('sprints.name', $direction)->orderBy('sprints.id', 'desc'),
            'project'  => $query->orderByRaw("{$minProjectSql} {$direction}")->orderBy('sprints.id', 'desc'),
            'status'   => $query->orderBy('sprints.is_open', $direction)->orderBy('sprints.id', 'desc'),
            'duration' => $query->orderByRaw("{$durationSql} {$direction}")->orderBy('sprints.id', 'desc'),
            default    => $query->orderBy('sprints.id', $direction),
        };

        /** @var LengthAwarePaginator<int, Sprint> $paginator */
        $paginator = $query->with(['projects:id,name'])->paginate($state['per_page'])->withQueryString();

        $secondsBySprint = $this->totalSessionSecondsForSprintIds($paginator->getCollection()->pluck('id'));

        $formatter = app(DateIntervalFormatter::class);

        $sprints = $paginator->through(function (Sprint $sprint) use ($secondsBySprint, $formatter): Sprint {
            $seconds = $secondsBySprint[$sprint->id] ?? 0;
            $sprint->totalDurationForHumans = $formatter->forHumans($formatter->createFromSeconds($seconds));

            return $sprint;
        });

        return Inertia::render('Sprint/Index', [
            'sprints'  => $sprints,
            'projects' => Project::notArchived()->orderBy('name')->get(['id', 'name']),
            'table'    => [
                'filters' => [
                    'q'          => $state['q'],
                    'project_id' => $state['project_id'] !== null ? (string) $state['project_id'] : '',
                    'lifecycle'  => $state['lifecycle'] ?? '',
                ],
                'sort'      => $state['sort'],
                'direction' => $state['direction'],
                'per_page'  => $state['per_page'],
            ],
        ]);
    }

    private function minProjectNameSubquerySql(): string
    {
        return '(SELECT MIN(projects.name) FROM project_sprint INNER JOIN projects ON projects.id = project_sprint.project_id WHERE project_sprint.sprint_id = sprints.id)';
    }

    private function durationSumSubquerySql(): string
    {
        return match (DB::connection()->getDriverName()) {
            'sqlite' => '(SELECT COALESCE(SUM(CASE WHEN sessions.ended_at IS NOT NULL THEN (strftime(\'%s\', sessions.ended_at) - strftime(\'%s\', sessions.started_at)) ELSE 0 END), 0) FROM sessions WHERE sessions.sprint_id = sprints.id)',
            default  => '(SELECT COALESCE(SUM(CASE WHEN sessions.ended_at IS NOT NULL THEN TIMESTAMPDIFF(SECOND, sessions.started_at, sessions.ended_at) ELSE 0 END), 0) FROM sessions WHERE sessions.sprint_id = sprints.id)',
        };
    }

    /**
     * @param Collection<int, int>|array<int, int> $sprintIds
     *
     * @return array<int, int>
     */
    private function totalSessionSecondsForSprintIds(Collection|array $sprintIds): array
    {
        $ids = $sprintIds instanceof Collection ? $sprintIds->all() : $sprintIds;

        if ($ids === []) {
            return [];
        }

        $sumExpr = match (DB::connection()->getDriverName()) {
            'sqlite' => 'SUM(CASE WHEN ended_at IS NOT NULL THEN (strftime(\'%s\', ended_at) - strftime(\'%s\', started_at)) ELSE 0 END)',
            default  => 'SUM(CASE WHEN ended_at IS NOT NULL THEN TIMESTAMPDIFF(SECOND, started_at, ended_at) ELSE 0 END)',
        };

        /** @var Collection<int, float|int|string> $rows */
        $rows = DB::table('sessions')
            ->selectRaw("sprint_id, {$sumExpr} as total_seconds")
            ->whereIn('sprint_id', $ids)
            ->groupBy('sprint_id')
            ->pluck('total_seconds', 'sprint_id');

        return $rows->map(fn ($v) => (int) $v)->all();
    }

    /**
     * Show the form for creating a new sprint.
     */
    public function create(): Response
    {
        return Inertia::render('Sprint/Edit', [
            'projects'        => Project::notArchived()->orderBy('name')->get(),
            'focusedClientId' => auth()->user()->focusedClientId(),
        ]);
    }

    /**
     * Save a new sprint to the database.
     */
    public function store(Request $request): RedirectResponse
    {
        $this->validate($request, [
            'name'          => 'required|unique:sprints,name',
            'project_ids'   => 'required|array|min:1',
            'project_ids.*' => 'exists:projects,id',
            'is_open'       => 'boolean',
        ]);

        $sprint = Sprint::create($request->only(['name', 'is_open']));
        $sprint->projects()->attach($request->project_ids);

        return redirect()
            ->route('sprint.index')
            ->with('success', 'Sprint "'.$sprint->name.'" created');
    }

    /**
     * Display a single sprint.
     */
    public function show(Sprint $sprint): Response
    {
        return Inertia::render('Sprint/Show', [
            'sprint' => $sprint->load('projects', 'sessions.task.project'),
            'tasks'  => $sprint->sessions->groupBy('task_id')->map(function ($sessionsForTask) {
                $task = $sessionsForTask->first()->task;

                $task->totalDurationInSprintForHumans = $sessionsForTask->totalDurationForHumans();

                return $task;
            }),
            'totalDurationForHumans' => $sprint->sessions->totalDurationForHumans(),
        ]);
    }

    /**
     * Show the form for editing the specified sprint.
     */
    public function edit(Sprint $sprint): Response
    {
        return Inertia::render('Sprint/Edit', [
            'projects' => Project::notArchived()->orderBy('name')->get(),
            'sprint'   => $sprint->load('projects'),
        ]);
    }

    /**
     * Update the specified sprint in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param Sprint                   $sprint
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request, Sprint $sprint): RedirectResponse
    {
        $this->validate($request, [
            'name'          => 'required|unique:sprints,name,'.$sprint->id,
            'project_ids'   => 'required|array|min:1',
            'project_ids.*' => 'exists:projects,id',
        ]);

        $sprint->update(['name' => $request->name, 'is_open' => $request->is_open == 'is_open' ? 1 : 0]);
        $sprint->projects()->sync($request->project_ids);

        return redirect()
            ->route('sprint.index')
            ->with('success', 'Sprint "'.$sprint->name.'" updated');
    }

    /**
     * Remove the specified sprint from storage.
     *
     * @param Sprint $sprint
     */
    public function destroy(Sprint $sprint): RedirectResponse
    {
        $sprint->delete();

        return redirect()
            ->route('sprint.index')
            ->with('success', '"Sprint '.$sprint->name.'" deleted');
    }
}
