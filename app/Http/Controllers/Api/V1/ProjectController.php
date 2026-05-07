<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\StoreProjectRequest;
use App\Http\Requests\Api\V1\UpdateProjectRequest;
use App\Http\Resources\V1\ProjectResource;
use App\Models\Project;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class ProjectController extends Controller
{
    public function index(): AnonymousResourceCollection
    {
        $projects = Project::query()
            ->with('client')
            ->orderBy('name')
            ->paginate();

        return ProjectResource::collection($projects);
    }

    public function store(StoreProjectRequest $request): JsonResponse
    {
        $project = Project::create($request->validated());

        return (new ProjectResource($project->load('client')))
            ->response()
            ->setStatusCode(201);
    }

    public function show(Project $project): ProjectResource
    {
        return new ProjectResource($project->load('client'));
    }

    public function update(UpdateProjectRequest $request, Project $project): ProjectResource
    {
        $project->update($request->validated());

        return new ProjectResource($project->fresh('client'));
    }

    public function destroy(Project $project): JsonResponse
    {
        if (!$project->isDeletable()) {
            return response()->json([
                'message' => 'Project cannot be deleted because it has associated tasks or sessions.',
            ], 422);
        }

        $project->delete();

        return response()->json(null, 204);
    }

    public function archive(Project $project): ProjectResource
    {
        $project->archive();

        return new ProjectResource($project->fresh('client'));
    }

    public function unarchive(Project $project): ProjectResource
    {
        $project->unarchive();

        return new ProjectResource($project->fresh('client'));
    }
}
