<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class DestroyManySessionsRequest extends FormRequest
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
            'session_ids'   => ['required', 'array', 'min:1'],
            'session_ids.*' => ['integer', 'exists:sessions,id'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'session_ids.required' => 'Select at least one session.',
            'session_ids.min'      => 'Select at least one session.',
        ];
    }
}
