<?php

namespace App\Http\Resources\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin \App\Models\Invoice */
class InvoiceResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id'          => $this->id,
            'number'      => $this->number,
            'date'        => $this->date,
            'due_date'    => $this->due_date,
            'amount'      => $this->amount,
            'total_hours' => $this->total_hours,
            'description' => $this->description,
            'is_paid'     => (bool) $this->is_paid,
            'is_sent'     => (bool) $this->is_sent,
            'client_id'   => $this->client_id,
            'client'      => new ClientResource($this->whenLoaded('client')),
            'created_at'  => $this->created_at?->toIso8601String(),
            'updated_at'  => $this->updated_at?->toIso8601String(),
        ];
    }
}
