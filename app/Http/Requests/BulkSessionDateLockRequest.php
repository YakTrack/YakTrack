<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class BulkSessionDateLockRequest extends FormRequest
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
            'dates'   => ['required', 'array', 'min:1'],
            'dates.*' => ['required', 'date_format:Y-m-d'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'dates.required' => 'Select at least one date.',
            'dates.min'      => 'Select at least one date.',
        ];
    }
}
