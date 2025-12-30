<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreFeatureRequest;
use App\Http\Requests\UpdateFeatureRequest;
use App\Models\Feature;
use App\Models\Project;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class FeatureController extends Controller
{
    public function index(Request $request): Response
    {
        $query = Feature::with(['project', 'acceptanceCriteria'])
            ->join('projects', 'features.project_id', '=', 'projects.id')
            ->where('features.is_active', true)
            ->select('features.*');

        if ($request->has('project_id')) {
            $query->where('features.project_id', $request->project_id);
        }

        $features = $query->orderBy('projects.name')
            ->orderBy('features.code')
            ->orderBy('features.name')
            ->paginate(15)
            ->withQueryString()
            ->through(fn ($feature) => $feature->append(['acceptance_criteria_count']));

        return Inertia::render('Features/Index', [
            'features' => $features,
            'projects' => Project::orderBy('name')->get(),
            'filters'  => $request->only(['project_id']),
        ]);
    }

    public function create(): Response
    {
        return Inertia::render('Features/Edit', [
            'projects' => Project::notArchived()->orderBy('name')->get(),
        ]);
    }

    public function store(StoreFeatureRequest $request): RedirectResponse
    {
        $feature = Feature::create($request->validated());

        return redirect()->route('features.show', $feature)
            ->with('success', 'Feature created successfully.');
    }

    public function show(Feature $feature): Response
    {
        $feature->load(['project', 'acceptanceCriteria' => function ($query) {
            $query->where('is_active', true)
                ->orderByRaw('code IS NULL ASC')
                ->orderBy('code', 'asc')
                ->orderBy('name', 'asc');
        }]);

        return Inertia::render('Features/Show', [
            'feature' => $feature,
        ]);
    }

    public function edit(Feature $feature): Response
    {
        $feature->load('project');

        return Inertia::render('Features/Edit', [
            'feature'  => $feature,
            'projects' => Project::notArchived()->orderBy('name')->get(),
        ]);
    }

    public function update(UpdateFeatureRequest $request, Feature $feature): RedirectResponse
    {
        $feature->update($request->validated());

        return redirect()->route('features.show', $feature)
            ->with('success', 'Feature updated successfully.');
    }

    public function destroy(Feature $feature): RedirectResponse
    {
        // Soft delete the feature
        $feature->update(['is_active' => false]);

        return redirect()->route('features.index')
            ->with('success', 'Feature deleted successfully.');
    }
}
