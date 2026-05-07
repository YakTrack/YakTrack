<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\StoreSprintRequest;
use App\Http\Requests\Api\V1\UpdateSprintRequest;
use App\Http\Resources\V1\SprintResource;
use App\Models\Sprint;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class SprintController extends Controller
{
    public function index(): AnonymousResourceCollection
    {
        $sprints = Sprint::query()
            ->with('projects')
            ->orderByDesc('id')
            ->paginate();

        return SprintResource::collection($sprints);
    }

    public function store(StoreSprintRequest $request): JsonResponse
    {
        $sprint = Sprint::create($request->safe()->only(['name', 'is_open']));
        $sprint->projects()->attach($request->validated('project_ids'));

        return (new SprintResource($sprint->load('projects')))
            ->response()
            ->setStatusCode(201);
    }

    public function show(Sprint $sprint): SprintResource
    {
        return new SprintResource($sprint->load('projects'));
    }

    public function update(UpdateSprintRequest $request, Sprint $sprint): SprintResource
    {
        $sprint->update($request->safe()->only(['name', 'is_open']));

        if ($request->has('project_ids')) {
            $sprint->projects()->sync($request->validated('project_ids'));
        }

        return new SprintResource($sprint->fresh('projects'));
    }

    public function destroy(Sprint $sprint): JsonResponse
    {
        $sprint->delete();

        return response()->json(null, 204);
    }
}
