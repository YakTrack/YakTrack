<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreAcceptanceCriteriaRequest;
use App\Http\Requests\UpdateAcceptanceCriteriaRequest;
use App\Models\AcceptanceCriteria;
use App\Models\Project;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class AcceptanceCriteriaController extends Controller
{
    public function index(Request $request): Response
    {
        $query = AcceptanceCriteria::with(['project', 'versions', 'tasks'])
            ->where('is_active', true);

        if ($request->has('project_id')) {
            $query->where('project_id', $request->project_id);
        }

        $criteria = $query->orderBy('name')
            ->paginate(15)
            ->withQueryString()
            ->through(fn ($criterion) => $criterion->append(['version_count', 'linked_tasks_count']));

        return Inertia::render('AcceptanceCriteria/Index', [
            'criteria' => $criteria,
            'projects' => Project::orderBy('name')->get(),
            'filters' => $request->only(['project_id']),
        ]);
    }

    public function create(): Response
    {
        return Inertia::render('AcceptanceCriteria/Edit', [
            'projects' => Project::orderBy('name')->get(),
        ]);
    }

    public function store(StoreAcceptanceCriteriaRequest $request): RedirectResponse
    {
        $criteria = AcceptanceCriteria::create($request->validated());

        // Create initial version
        $criteria->createVersion($request->validated(), auth()->id());

        return redirect()->route('acceptance-criteria.index')
            ->with('success', 'Acceptance criteria "' . $criteria->name . '" has been created.');
    }

    public function show(AcceptanceCriteria $acceptance_criterion): Response
    {
        $acceptance_criterion->load([
            'project',
            'versions.changedByUser',
            'tasks',
            'testResults.testRun.executedByUser',
        ]);

        return Inertia::render('AcceptanceCriteria/Show', [
            'criteria' => $acceptance_criterion,
        ]);
    }

    public function edit(AcceptanceCriteria $acceptance_criterion): Response
    {
        return Inertia::render('AcceptanceCriteria/Edit', [
            'criteria' => $acceptance_criterion,
            'projects' => Project::orderBy('name')->get(),
        ]);
    }

    public function update(UpdateAcceptanceCriteriaRequest $request, AcceptanceCriteria $acceptance_criterion): RedirectResponse
    {
        $acceptance_criterion->updateWithVersion($request->validated(), auth()->id());

        return redirect()->route('acceptance-criteria.show', $acceptance_criterion)
            ->with('success', 'Acceptance criteria "' . $acceptance_criterion->name . '" has been updated.');
    }

    public function destroy(AcceptanceCriteria $acceptance_criterion): RedirectResponse
    {
        $name = $acceptance_criterion->name;
        
        $acceptance_criterion->update(['is_active' => false]);

        return redirect()->route('acceptance-criteria.index')
            ->with('success', 'Acceptance criteria "' . $name . '" has been deleted.');
    }
}