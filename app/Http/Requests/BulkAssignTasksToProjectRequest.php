<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class BulkAssignTasksToProjectRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'project_id'   => [
                'required',
                'integer',
                Rule::exists('projects', 'id')->where(fn ($query) => $query->whereNull('archived_at')),
            ],
            'task_ids'     => ['required', 'array', 'min:1'],
            'task_ids.*'   => ['integer', 'exists:tasks,id'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'task_ids.required' => 'Select at least one task.',
            'task_ids.min'      => 'Select at least one task.',
        ];
    }
}
