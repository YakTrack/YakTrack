<?php

namespace App\Http\Requests\Api\V1;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreTaskRequest extends FormRequest
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
            'name' => [
                'required',
                'string',
                'max:255',
                Rule::unique('tasks')->where(function ($query) {
                    return $query->where('project_id', $this->input('project_id'));
                }),
            ],
            'description'  => 'nullable|string',
            'project_id'   => 'nullable|exists:projects,id',
            'status_id'    => 'nullable|exists:task_statuses,id',
            'is_billable'  => 'nullable|boolean',
        ];
    }
}
