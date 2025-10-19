<?php

namespace App\Http\Controllers;

use App\Http\Requests\ImportAcceptanceCriteriaRequest;
use App\Http\Requests\StoreAcceptanceCriteriaRequest;
use App\Http\Requests\UpdateAcceptanceCriteriaRequest;
use App\Models\AcceptanceCriteria;
use App\Models\Feature;
use App\Models\Project;
use App\Services\GherkinParser;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;
use Inertia\Response;

class AcceptanceCriteriaController extends Controller
{
    public function index(Request $request): Response
    {
        $query = AcceptanceCriteria::with(['project', 'feature', 'versions', 'tasks'])
            ->where('is_active', true);

        if ($request->has('project_id')) {
            $query->where('project_id', $request->project_id);
        }

        if ($request->has('feature_id')) {
            $query->where('feature_id', $request->feature_id);
        }

        $criteria = $query->orderBy('feature_id')
            ->orderBy('name')
            ->paginate(15)
            ->withQueryString()
            ->through(fn ($criterion) => $criterion->append(['version_count', 'linked_tasks_count']));

        // Get available features for filtering
        $availableFeatures = [];
        if ($request->has('project_id')) {
            $availableFeatures = Feature::getForProject($request->project_id);
        }

        return Inertia::render('AcceptanceCriteria/Index', [
            'criteria' => $criteria,
            'projects' => Project::orderBy('name')->get(),
            'availableFeatures' => $availableFeatures,
            'filters' => $request->only(['project_id', 'feature_id']),
        ]);
    }

    public function create(): Response
    {
        return Inertia::render('AcceptanceCriteria/Edit', [
            'projects' => Project::orderBy('name')->get(),
            'features' => Feature::where('is_active', true)->orderBy('name')->get(),
        ]);
    }

    public function store(StoreAcceptanceCriteriaRequest $request): RedirectResponse
    {
        $criteria = AcceptanceCriteria::create($request->validated());

        // Create initial version
        $criteria->createVersion($request->validated(), auth()->id());

        return redirect()->route('acceptance-criteria.index')
            ->with('success', 'Acceptance criteria "'.$criteria->name.'" has been created.');
    }

    public function show(AcceptanceCriteria $acceptance_criterion): Response
    {
        $acceptance_criterion->load([
            'project',
            'feature',
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
            'features' => Feature::where('is_active', true)->orderBy('name')->get(),
        ]);
    }

    public function update(UpdateAcceptanceCriteriaRequest $request, AcceptanceCriteria $acceptance_criterion): RedirectResponse
    {
        $acceptance_criterion->updateWithVersion($request->validated(), auth()->id());

        return redirect()->route('acceptance-criteria.show', $acceptance_criterion)
            ->with('success', 'Acceptance criteria "'.$acceptance_criterion->name.'" has been updated.');
    }

    public function destroy(AcceptanceCriteria $acceptance_criterion): RedirectResponse
    {
        $name = $acceptance_criterion->name;

        $acceptance_criterion->update(['is_active' => false]);

        return redirect()->route('acceptance-criteria.index')
            ->with('success', 'Acceptance criteria "'.$name.'" has been deleted.');
    }

    public function import(): Response
    {
        return Inertia::render('AcceptanceCriteria/Import', [
            'projects' => Project::orderBy('name')->get(),
        ]);
    }

    public function processImport(ImportAcceptanceCriteriaRequest $request): RedirectResponse
    {
        $file = $request->file('file');
        $content = file_get_contents($file->getPathname());
        $project = Project::findOrFail($request->project_id);
        $overwriteExisting = $request->boolean('overwrite_existing');

        $parser = new GherkinParser();
        $criteriaData = $parser->parse($content, $project);

        $importedCount = 0;
        $skippedCount = 0;
        $errors = [];

        DB::transaction(function () use ($criteriaData, $project, $overwriteExisting, &$importedCount, &$skippedCount, &$errors) {
            foreach ($criteriaData as $data) {
                try {
                    // Check if criteria with same code already exists
                    $existingCriteria = AcceptanceCriteria::where('project_id', $project->id)
                        ->where('code', $data['code'])
                        ->first();

                    if ($existingCriteria && !$overwriteExisting) {
                        $skippedCount++;
                        continue;
                    }

                    if ($existingCriteria && $overwriteExisting) {
                        // Update existing criteria
                        $existingCriteria->updateWithVersion([
                            'name' => $data['name'],
                            'description' => $data['description'],
                            'feature_id' => $data['feature_id'] ?? null,
                        ], auth()->id());
                        $importedCount++;
                    } else {
                        // Create new criteria
                        $criteria = AcceptanceCriteria::create([
                            'project_id' => $project->id,
                            'code' => $data['code'],
                            'name' => $data['name'],
                            'description' => $data['description'],
                            'feature_id' => $data['feature_id'] ?? null,
                            'is_active' => true,
                        ]);

                        // Create initial version
                        $criteria->createVersion($data, auth()->id());
                        $importedCount++;
                    }
                } catch (\Exception $e) {
                    $errors[] = "Failed to import '{$data['name']}': " . $e->getMessage();
                }
            }
        });

        $message = "Import completed. {$importedCount} acceptance criteria imported successfully.";
        
        if ($skippedCount > 0) {
            $message .= " {$skippedCount} criteria were skipped (already exist).";
        }

        if (!empty($errors)) {
            $message .= " " . count($errors) . " errors occurred.";
        }

        return redirect()->route('acceptance-criteria.index')
            ->with('success', $message)
            ->with('import_errors', $errors);
    }
}
