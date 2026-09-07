<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreSessionPendingTaskRequest;
use App\Models\Session;
use App\Models\SessionPendingTask;
use App\Models\Task;
use App\Services\SessionDateLockService;
use Illuminate\Database\QueryException;
use Illuminate\Http\JsonResponse;

class SessionPendingTaskController extends Controller
{
    public function __construct(private SessionDateLockService $sessionDateLockService)
    {
    }

    public function index(Session $session): JsonResponse
    {
        // Pure read: viewing links is allowed even on a locked date, and never
        // seeds a link (that would be a read side-effect now that finished
        // sessions are viewable too).
        return response()->json([
            'pending_tasks' => $this->pendingTasksPayload($session),
        ]);
    }

    public function store(StoreSessionPendingTaskRequest $request, Session $session): JsonResponse
    {
        $this->sessionDateLockService->ensureSessionCanBeEdited($session);

        // Seed the session's own task as a split part exactly once, ever. The flag
        // (not "zero links") is the trigger, so once the user has removed the seeded
        // task it is not resurrected when the set is later emptied and re-added.
        // The seeded task is preserved even if its project is archived / status is
        // closed: it is historical fact for this session, so the archived/closed
        // rule in StoreSessionPendingTaskRequest intentionally applies only to the
        // explicitly requested task, not to this seed.
        if ($session->task_id && is_null($session->pending_tasks_seeded_at)) {
            $this->linkTask($session, (int) $session->task_id);

            $session->forceFill(['pending_tasks_seeded_at' => now()])->save();
        }

        $this->linkTask($session, (int) $request->input('task_id'));

        return response()->json([
            'pending_tasks' => $this->pendingTasksPayload($session),
        ]);
    }

    public function destroy(Session $session, Task $task): JsonResponse
    {
        $this->sessionDateLockService->ensureSessionCanBeEdited($session);

        $session->pendingTasks()->where('task_id', $task->id)->delete();

        return response()->json([
            'pending_tasks' => $this->pendingTasksPayload($session),
        ]);
    }

    /**
     * Link a task to the session, tolerating a concurrent insert that would
     * otherwise violate the (session_id, task_id) unique index.
     */
    private function linkTask(Session $session, int $taskId): void
    {
        try {
            $session->pendingTasks()->firstOrCreate(['task_id' => $taskId]);
        } catch (QueryException $exception) {
            // 23000 is the integrity-constraint-violation SQLSTATE (unique index)
            // on both MySQL and SQLite; the row was created concurrently, so the
            // link already exists and there is nothing more to do.
            if ($exception->getCode() !== '23000') {
                throw $exception;
            }
        }
    }

    /**
     * @return array<int, array{id: int, task_id: int, task_name: string|null, project_name: string|null, client_name: string|null}>
     */
    private function pendingTasksPayload(Session $session): array
    {
        return $session->pendingTasks()
            ->with([
                'task:id,name,project_id',
                'task.project:id,name,client_id',
                'task.project.client:id,name',
            ])
            ->get()
            ->map(fn (SessionPendingTask $pending): array => [
                'id'           => $pending->id,
                'task_id'      => $pending->task_id,
                'task_name'    => $pending->task?->name,
                'project_name' => $pending->task?->project?->name,
                'client_name'  => $pending->task?->project?->client?->name,
            ])
            ->values()
            ->all();
    }
}
