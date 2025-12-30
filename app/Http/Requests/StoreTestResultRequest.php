<?php

namespace App\Http\Requests;

use App\TestResultStatus;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreTestResultRequest extends FormRequest
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
            'acceptance_criteria_id' => [
                'required',
                'exists:acceptance_criteria,id',
            ],
            'status' => [
                'nullable',
                Rule::enum(TestResultStatus::class),
            ],
            'notes' => 'nullable|string',
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
            'acceptance_criteria_id.required' => 'Please select an acceptance criteria.',
            'acceptance_criteria_id.exists'   => 'The selected acceptance criteria does not exist.',
            'status.enum'                     => 'The test result status must be one of: pending, passed, failed, skipped, blocked.',
        ];
    }
}
