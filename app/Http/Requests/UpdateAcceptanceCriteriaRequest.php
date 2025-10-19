<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateAcceptanceCriteriaRequest extends FormRequest
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
        $criteria = $this->route('acceptance_criterion');
        $criteriaId = $criteria ? $criteria->id : null;

        return [
            'project_id' => 'required|exists:projects,id',
            'feature_id' => [
                'nullable',
                'exists:features,id',
                Rule::exists('features', 'id')->where(function ($query) {
                    return $query->where('project_id', $this->project_id)
                        ->where('is_active', true);
                }),
            ],
            'code' => [
                'nullable',
                'string',
                'max:50',
                Rule::unique('acceptance_criteria', 'code')
                    ->where(function ($query) {
                        return $query->where('project_id', $this->project_id);
                    })
                    ->when($criteriaId, function ($rule) use ($criteriaId) {
                        return $rule->ignore($criteriaId);
                    }),
            ],
            'name'        => 'required|string|max:255',
            'description' => 'nullable|string',
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
            'project_id.required' => 'A project must be selected.',
            'project_id.exists'   => 'The selected project does not exist.',
            'feature_id.exists'   => 'The selected feature does not exist or is not active for this project.',
            'code.unique'         => 'This code already exists for the selected project.',
            'name.required'       => 'The acceptance criteria name is required.',
            'name.max'            => 'The acceptance criteria name may not be greater than 255 characters.',
        ];
    }
}
