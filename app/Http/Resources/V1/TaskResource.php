<?php

namespace App\Http\Resources\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin \App\Models\Task */
class TaskResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id'          => $this->id,
            'name'        => $this->name,
            'description' => $this->description,
            'status'      => $this->status,
            'is_billable' => $this->is_billable,
            'project_id'  => $this->project_id,
            'status_id'   => $this->status_id,
            'project'     => new ProjectResource($this->whenLoaded('project')),
            'task_status' => $this->whenLoaded('taskStatus', fn () => [
                'id'   => $this->taskStatus->id,
                'name' => $this->taskStatus->name,
            ]),
            'created_at'  => $this->created_at?->toIso8601String(),
            'updated_at'  => $this->updated_at?->toIso8601String(),
        ];
    }
}
