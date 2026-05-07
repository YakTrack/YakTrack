<?php

namespace App\Http\Requests\Api\V1;

use Illuminate\Foundation\Http\FormRequest;

class UpdateSessionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'started_at'          => 'sometimes|required|date',
            'ended_at'            => 'nullable|date|after:started_at',
            'comment'             => 'nullable|string',
            'is_billable'         => 'nullable|boolean',
            'task_id'             => 'nullable|exists:tasks,id',
            'invoice_id'          => 'nullable|exists:invoices,id',
            'sprint_id'           => 'nullable|exists:sprints,id',
            'session_category_id' => 'nullable|exists:session_categories,id',
        ];
    }
}
