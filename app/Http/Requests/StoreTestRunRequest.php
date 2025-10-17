<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreTestRunRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'project_id'                => 'required|exists:projects,id',
            'name'                      => 'required|string|max:255',
            'description'               => 'nullable|string',
            'executed_at'               => 'required|date',
            'acceptance_criteria_ids'   => 'required|array|min:1',
            'acceptance_criteria_ids.*' => 'exists:acceptance_criteria,id',
        ];
    }

    /**
     * Get custom error messages for validation rules.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'project_id.required'              => 'A project must be selected.',
            'project_id.exists'                => 'The selected project does not exist.',
            'name.required'                    => 'The test run name is required.',
            'name.max'                         => 'The test run name may not be greater than 255 characters.',
            'executed_at.required'             => 'The execution date is required.',
            'executed_at.date'                 => 'The execution date must be a valid date.',
            'acceptance_criteria_ids.required' => 'At least one acceptance criteria must be selected.',
            'acceptance_criteria_ids.array'    => 'Acceptance criteria must be provided as a list.',
            'acceptance_criteria_ids.min'      => 'At least one acceptance criteria must be selected.',
            'acceptance_criteria_ids.*.exists' => 'One or more selected acceptance criteria do not exist.',
        ];
    }
}
