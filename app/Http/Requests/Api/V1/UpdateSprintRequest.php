<?php

namespace App\Http\Requests\Api\V1;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateSprintRequest extends FormRequest
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
                'sometimes',
                'required',
                'string',
                'max:255',
                Rule::unique('sprints')->ignore($this->route('sprint')),
            ],
            'is_open'       => 'nullable|boolean',
            'project_ids'   => 'sometimes|required|array|min:1',
            'project_ids.*' => 'exists:projects,id',
        ];
    }
}
