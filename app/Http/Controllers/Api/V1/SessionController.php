<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\StoreSessionRequest;
use App\Http\Requests\Api\V1\UpdateSessionRequest;
use App\Http\Resources\V1\SessionResource;
use App\Models\Session;
use App\Models\Sprint;
use App\Support\DateTimeFormatter;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class SessionController extends Controller
{
    public function __construct(private DateTimeFormatter $dateTimeFormatter)
    {
    }

    public function index(): AnonymousResourceCollection
    {
        $sessions = Session::query()
            ->with('task.project')
            ->orderByDesc('id')
            ->paginate();

        return SessionResource::collection($sessions);
    }

    public function store(StoreSessionRequest $request): JsonResponse
    {
        if ($request->validated('ended_at') === null) {
            Session::running()->get()->each(fn (Session $session) => $session->stop());
        }

        $session = Session::create([
            'started_at'          => $this->dateTimeFormatter->utcFormat($request->validated('started_at')),
            'ended_at'            => $request->validated('ended_at') ? $this->dateTimeFormatter->utcFormat($request->validated('ended_at')) : null,
            'task_id'             => $request->validated('task_id'),
            'invoice_id'          => $request->validated('invoice_id'),
            'sprint_id'           => $request->validated('sprint_id'),
            'session_category_id' => $request->validated('session_category_id'),
            'comment'             => $request->validated('comment'),
            'is_billable'         => $request->validated('is_billable', true),
        ]);

        return (new SessionResource($session->load('task.project')))
            ->response()
            ->setStatusCode(201);
    }

    public function show(Session $session): SessionResource
    {
        return new SessionResource($session->load('task.project'));
    }

    public function update(UpdateSessionRequest $request, Session $session): SessionResource
    {
        $data = $request->validated();

        if (isset($data['started_at'])) {
            $data['started_at'] = $this->dateTimeFormatter->utcFormat($data['started_at']);
        }

        if (array_key_exists('ended_at', $data)) {
            $data['ended_at'] = $data['ended_at'] ? $this->dateTimeFormatter->utcFormat($data['ended_at']) : null;
        }

        $session->update($data);

        return new SessionResource($session->fresh('task.project'));
    }

    public function destroy(Session $session): JsonResponse
    {
        $session->delete();

        return response()->json(null, 204);
    }

    public function start(): JsonResponse
    {
        Session::running()->get()->each(fn (Session $session) => $session->stop());

        $session = Session::create([
            'started_at'  => Carbon::now(),
            'is_billable' => true,
        ]);

        return (new SessionResource($session))
            ->response()
            ->setStatusCode(201);
    }

    public function stop(): JsonResponse
    {
        $stopped = Session::running()->get();
        $stopped->each(fn (Session $session) => $session->stop());

        return response()->json([
            'message' => $stopped->count().' session(s) stopped.',
            'stopped' => SessionResource::collection($stopped->map->fresh()),
        ]);
    }

    public function continue(Session $session): JsonResponse
    {
        Session::running()->get()->each(fn (Session $s) => $s->stop());

        $newSprintId = null;

        if ($session->sprint_id) {
            $projectIds = $session->sprint->projects()->pluck('id');
            $openSprint = Sprint::open()
                ->whereHas('projects', fn ($q) => $q->whereIn('id', $projectIds))
                ->orderByDesc('id')
                ->first();
            $newSprintId = $openSprint?->id ?? $session->sprint_id;
        }

        $newSession = Session::create([
            'started_at'          => Carbon::now(),
            'is_billable'         => $session->is_billable ?? true,
            'task_id'             => $session->task_id,
            'sprint_id'           => $newSprintId,
            'session_category_id' => $session->session_category_id,
        ]);

        return (new SessionResource($newSession->load('task.project')))
            ->response()
            ->setStatusCode(201);
    }
}
