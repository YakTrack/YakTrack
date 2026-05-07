<?php

namespace App\Http\Requests\Api\V1;

use Illuminate\Foundation\Http\FormRequest;

class StoreSprintRequest extends FormRequest
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
            'name'          => 'required|string|max:255|unique:sprints,name',
            'is_open'       => 'nullable|boolean',
            'project_ids'   => 'required|array|min:1',
            'project_ids.*' => 'exists:projects,id',
        ];
    }
}
