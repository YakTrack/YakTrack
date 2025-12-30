<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreFeatureRequest extends FormRequest
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
            'project_id' => ['required', 'integer', 'exists:projects,id'],
            'code'       => [
                'nullable',
                'string',
                'max:255',
                Rule::unique('features')->where(function ($query) {
                    return $query->where('project_id', $this->project_id);
                }),
            ],
            'name'       => [
                'required',
                'string',
                'max:255',
                Rule::unique('features')->where(function ($query) {
                    return $query->where('project_id', $this->project_id)
                        ->where('is_active', true);
                }),
            ],
            'description' => ['nullable', 'string'],
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'project_id.required' => 'Please select a project.',
            'project_id.exists'   => 'The selected project does not exist.',
            'code.unique'         => 'A feature with this code already exists for the selected project.',
            'code.max'            => 'Feature code must not exceed 255 characters.',
            'name.required'       => 'Feature name is required.',
            'name.unique'         => 'A feature with this name already exists for the selected project.',
            'name.max'            => 'Feature name must not exceed 255 characters.',
        ];
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation(): void
    {
        $this->merge([
            'code'        => $this->code ?: null,
            'description' => $this->description ?: null,
        ]);
    }
}
