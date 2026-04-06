<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class SprintIndexRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'sort'      => $this->input('sort', 'id'),
            'direction' => $this->input('direction', 'desc'),
            'per_page'  => (int) $this->input('per_page', 15),
        ]);

        if ($this->input('project_id') === '' || $this->input('project_id') === null) {
            $this->merge(['project_id' => null]);
        }

        if ($this->input('lifecycle') === '' || $this->input('lifecycle') === null) {
            $this->merge(['lifecycle' => null]);
        }
    }

    /**
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'q'          => ['nullable', 'string', 'max:255'],
            'project_id' => ['nullable', 'integer', 'exists:projects,id'],
            'lifecycle'  => ['nullable', 'string', Rule::in(['open', 'closed'])],
            'sort'       => ['required', 'string', Rule::in(['id', 'name', 'project', 'status', 'duration'])],
            'direction'  => ['required', 'string', Rule::in(['asc', 'desc'])],
            'per_page'   => ['required', 'integer', Rule::in([10, 15, 25, 50])],
        ];
    }

    /**
     * @return array{
     *     q: string,
     *     project_id: int|null,
     *     lifecycle: string|null,
     *     sort: string,
     *     direction: string,
     *     per_page: int
     * }
     */
    public function tableState(): array
    {
        /** @var array{
         *     q?: string|null,
         *     project_id?: int|null,
         *     lifecycle?: string|null,
         *     sort: string,
         *     direction: string,
         *     per_page: int
         * } $validated */
        $validated = $this->validated();

        return [
            'q'          => isset($validated['q']) ? trim((string) $validated['q']) : '',
            'project_id' => $validated['project_id'] ?? null,
            'lifecycle'  => $validated['lifecycle'] ?? null,
            'sort'       => $validated['sort'],
            'direction'  => $validated['direction'],
            'per_page'   => $validated['per_page'],
        ];
    }
}
