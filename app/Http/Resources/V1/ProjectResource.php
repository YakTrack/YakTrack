<?php

namespace App\Http\Resources\V1;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin \App\Models\Project */
class ProjectResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id'               => $this->id,
            'name'             => $this->name,
            'description'      => $this->description,
            'task_code_prefix' => $this->task_code_prefix,
            'is_billable'      => $this->is_billable,
            'archived_at'      => $this->archived_at ? Carbon::parse($this->archived_at)->toIso8601String() : null,
            'client_id'        => $this->client_id,
            'client'           => new ClientResource($this->whenLoaded('client')),
            'created_at'       => $this->created_at?->toIso8601String(),
            'updated_at'       => $this->updated_at?->toIso8601String(),
        ];
    }
}
