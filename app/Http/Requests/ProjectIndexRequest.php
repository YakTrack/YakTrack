<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ProjectIndexRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'sort'      => $this->input('sort', 'name'),
            'direction' => $this->input('direction', 'asc'),
            'per_page'  => (int) $this->input('per_page', 15),
        ]);

        if ($this->input('client_id') === '' || $this->input('client_id') === null) {
            $this->merge(['client_id' => null]);
        }
    }

    /**
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'q'         => ['nullable', 'string', 'max:255'],
            'client_id' => ['nullable', 'integer', 'exists:clients,id'],
            'sort'      => ['required', 'string', Rule::in(['name', 'client'])],
            'direction' => ['required', 'string', Rule::in(['asc', 'desc'])],
            'per_page'  => ['required', 'integer', Rule::in([10, 15, 25, 50])],
        ];
    }

    /**
     * @return array{q: string, client_id: int|null, sort: string, direction: string, per_page: int}
     */
    public function tableState(): array
    {
        /** @var array{q?: string|null, client_id?: int|null, sort: string, direction: string, per_page: int} $validated */
        $validated = $this->validated();

        return [
            'q'         => isset($validated['q']) ? trim((string) $validated['q']) : '',
            'client_id' => $validated['client_id'] ?? null,
            'sort'      => $validated['sort'],
            'direction' => $validated['direction'],
            'per_page'  => $validated['per_page'],
        ];
    }
}
