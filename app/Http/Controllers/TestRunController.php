<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreTestResultRequest;
use App\Http\Requests\StoreTestRunRequest;
use App\Models\AcceptanceCriteria;
use App\Models\Project;
use App\Models\TestResult;
use App\Models\TestRun;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class TestRunController extends Controller
{
    public function index(Request $request): Response
    {
        $query = TestRun::with(['project', 'executedByUser', 'testResults'])
            ->forFocusedClient($request->user()->focusedClientId());

        if ($request->has('project_id')) {
            $query->where('project_id', $request->project_id);
        }

        $testRuns = $query->orderBy('executed_at', 'desc')
            ->paginate(15)
            ->withQueryString()
            ->through(fn ($testRun) => $testRun->append(['pass_rate', 'completion_rate']));

        return Inertia::render('TestRun/Index', [
            'testRuns' => $testRuns,
            'projects' => Project::orderBy('name')->get(),
            'filters'  => $request->only(['project_id']),
        ]);
    }

    public function create(Request $request): Response
    {
        $projectId = $request->get('project_id');
        $criteria = $projectId
            ? AcceptanceCriteria::where('project_id', $projectId)->where('is_active', true)->orderBy('name')->get()
            : collect();

        return Inertia::render('TestRun/Create', [
            'projects'          => Project::notArchived()->orderBy('name')->get(),
            'criteria'          => $criteria,
            'selectedProjectId' => $projectId,
            'focusedClientId'   => auth()->user()->focusedClientId(),
        ]);
    }

    public function store(StoreTestRunRequest $request): RedirectResponse
    {
        $validated = $request->validated();

        $testRun = TestRun::create([
            'project_id'          => $validated['project_id'],
            'name'                => $validated['name'],
            'description'         => $validated['description'] ?? null,
            'executed_at'         => $validated['executed_at'],
            'executed_by_user_id' => auth()->id(),
        ]);

        // Create test results for selected criteria
        foreach ($validated['acceptance_criteria_ids'] as $criteriaId) {
            $criteria = AcceptanceCriteria::find($criteriaId);
            $latestVersion = $criteria->getCurrentVersion();

            TestResult::create([
                'test_run_id'                    => $testRun->id,
                'acceptance_criteria_id'         => $criteriaId,
                'acceptance_criteria_version_id' => $latestVersion?->id,
                'status'                         => 'pending', // Default status
            ]);
        }

        return redirect()->route('test-run.show', $testRun)
            ->with('success', 'Test run "'.$testRun->name.'" has been created.');
    }

    public function show(TestRun $testRun): Response
    {
        $testRun->load([
            'project',
            'executedByUser',
            'testResults' => function ($query) {
                $query->join('acceptance_criteria', 'test_results.acceptance_criteria_id', '=', 'acceptance_criteria.id')
                    ->orderByRaw('acceptance_criteria.code IS NULL ASC')
                    ->orderBy('acceptance_criteria.code', 'asc')
                    ->orderBy('acceptance_criteria.name', 'asc')
                    ->select('test_results.*');
            },
            'testResults.acceptanceCriteria',
            'testResults.acceptanceCriteriaVersion',
            'testResults.evidence',
        ]);

        // Get available acceptance criteria (not already in test run)
        $existingCriteriaIds = $testRun->testResults()->pluck('acceptance_criteria_id')->toArray();
        $availableCriteria = AcceptanceCriteria::where('project_id', $testRun->project_id)
            ->where('is_active', true)
            ->whereNotIn('id', $existingCriteriaIds)
            ->orderByRaw('code IS NULL ASC')
            ->orderBy('code', 'asc')
            ->orderBy('name', 'asc')
            ->get();

        return Inertia::render('TestRun/Show', [
            'testRun'           => $testRun,
            'summary'           => $testRun->getSummaryStatistics(),
            'availableCriteria' => $availableCriteria,
        ]);
    }

    public function storeTestResult(StoreTestResultRequest $request, TestRun $testRun): RedirectResponse
    {
        $validated = $request->validated();

        // Check if acceptance criteria already exists in test run
        $existingResult = $testRun->testResults()
            ->where('acceptance_criteria_id', $validated['acceptance_criteria_id'])
            ->first();

        if ($existingResult) {
            return redirect()->back()
                ->with('error', 'This acceptance criteria is already in the test run.');
        }

        // Verify the acceptance criteria belongs to the test run's project
        $criteria = AcceptanceCriteria::findOrFail($validated['acceptance_criteria_id']);
        if ($criteria->project_id !== $testRun->project_id) {
            return redirect()->back()
                ->with('error', 'The acceptance criteria must belong to the same project as the test run.');
        }

        $latestVersion = $criteria->getCurrentVersion();

        TestResult::create([
            'test_run_id'                    => $testRun->id,
            'acceptance_criteria_id'         => $validated['acceptance_criteria_id'],
            'acceptance_criteria_version_id' => $latestVersion?->id,
            'status'                         => $validated['status'] ?? 'pending',
            'notes'                          => $validated['notes'] ?? null,
        ]);

        return redirect()->back()
            ->with('success', 'Acceptance criteria has been added to the test run.');
    }

    public function destroy(TestRun $testRun): RedirectResponse
    {
        $name = $testRun->name;

        $testRun->delete();

        return redirect()->route('test-run.index')
            ->with('success', 'Test run "'.$name.'" has been deleted.');
    }
}
