<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\StoreTaskRequest;
use App\Http\Requests\Api\V1\UpdateTaskRequest;
use App\Http\Resources\V1\TaskResource;
use App\Models\Task;
use App\Models\TaskStatus;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class TaskController extends Controller
{
    public function index(): AnonymousResourceCollection
    {
        $tasks = Task::query()
            ->with(['project.client', 'taskStatus'])
            ->orderByDesc('id')
            ->paginate();

        return TaskResource::collection($tasks);
    }

    public function store(StoreTaskRequest $request): JsonResponse
    {
        $statusId = $request->validated('status_id');

        if (!$statusId && $request->validated('project_id')) {
            $defaultStatus = TaskStatus::where('project_id', $request->validated('project_id'))
                ->where('is_default', true)
                ->first();
            $statusId = $defaultStatus?->id;
        }

        $task = Task::create(array_merge($request->validated(), [
            'description' => $request->validated('description', ''),
            'status'      => 'incomplete',
            'status_id'   => $statusId,
        ]));

        return (new TaskResource($task->load(['project.client', 'taskStatus'])))
            ->response()
            ->setStatusCode(201);
    }

    public function show(Task $task): TaskResource
    {
        return new TaskResource($task->load(['project.client', 'taskStatus']));
    }

    public function update(UpdateTaskRequest $request, Task $task): TaskResource
    {
        $task->update($request->validated());

        return new TaskResource($task->fresh(['project.client', 'taskStatus']));
    }

    public function destroy(Task $task): JsonResponse
    {
        $task->delete();

        return response()->json(null, 204);
    }
}
