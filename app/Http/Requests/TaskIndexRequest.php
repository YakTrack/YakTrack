<?php

namespace App\Http\Requests;

use App\Models\TaskStatus;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Validator;

class TaskIndexRequest extends FormRequest
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

        if ($this->input('status_id') === '' || $this->input('status_id') === null) {
            $this->merge(['status_id' => null]);
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
            'status_id'  => ['nullable', 'integer', 'exists:task_statuses,id'],
            'sort'       => ['required', 'string', Rule::in(['id', 'name', 'project', 'client', 'status'])],
            'direction'  => ['required', 'string', Rule::in(['asc', 'desc'])],
            'per_page'   => ['required', 'integer', Rule::in([10, 15, 25, 50])],
        ];
    }

    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $validator): void {
            $projectId = $this->input('project_id');
            $statusId = $this->input('status_id');
            if ($projectId && $statusId
                && ! TaskStatus::query()->whereKey($statusId)->where('project_id', $projectId)->exists()) {
                $validator->errors()->add(
                    'status_id',
                    'The selected status is invalid for this project.',
                );
            }
        });
    }

    /**
     * @return array{
     *     q: string,
     *     project_id: int|null,
     *     status_id: int|null,
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
         *     status_id?: int|null,
         *     sort: string,
         *     direction: string,
         *     per_page: int
         * } $validated */
        $validated = $this->validated();

        return [
            'q'          => isset($validated['q']) ? trim((string) $validated['q']) : '',
            'project_id' => $validated['project_id'] ?? null,
            'status_id'  => $validated['status_id'] ?? null,
            'sort'       => $validated['sort'],
            'direction'  => $validated['direction'],
            'per_page'   => $validated['per_page'],
        ];
    }
}
