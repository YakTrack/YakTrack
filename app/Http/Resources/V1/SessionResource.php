<?php

namespace App\Http\Resources\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin \App\Models\Session */
class SessionResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id'                  => $this->id,
            'started_at'          => $this->started_at?->toIso8601String(),
            'ended_at'            => $this->ended_at?->toIso8601String(),
            'comment'             => $this->comment,
            'is_billable'         => $this->is_billable,
            'is_running'          => $this->isRunning,
            'duration_in_seconds' => $this->durationInSeconds,
            'duration_for_humans' => $this->durationForHumans,
            'task_id'             => $this->task_id,
            'invoice_id'          => $this->invoice_id,
            'sprint_id'           => $this->sprint_id,
            'session_category_id' => $this->session_category_id,
            'task'                => new TaskResource($this->whenLoaded('task')),
            'created_at'          => $this->created_at?->toIso8601String(),
            'updated_at'          => $this->updated_at?->toIso8601String(),
        ];
    }
}
