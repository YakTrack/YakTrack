<?php

namespace App\Http\Requests\Api\V1;

use Illuminate\Foundation\Http\FormRequest;

class StoreProjectRequest extends FormRequest
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
            'name'             => 'required|string|max:255',
            'description'      => 'nullable|string',
            'client_id'        => 'nullable|exists:clients,id',
            'task_code_prefix' => 'nullable|string|max:20',
            'is_billable'      => 'nullable|boolean',
        ];
    }
}
