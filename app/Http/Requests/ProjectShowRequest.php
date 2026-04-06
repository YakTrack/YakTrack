<?php

namespace App\Http\Requests;

use App\Models\Sprint;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ProjectShowRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'tab'       => $this->input('tab', 'overview'),
            'sort'      => $this->input('sort', 'ended_at'),
            'direction' => $this->input('direction', 'desc'),
            'per_page'  => (int) $this->input('per_page', 15),
            'q'         => $this->input('q', ''),
        ]);

        if ($this->input('sprint_id') === '') {
            $this->merge(['sprint_id' => null]);
        }

        if ($this->input('billable') === '') {
            $this->merge(['billable' => null]);
        }
    }

    /**
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'tab'       => ['required', 'string', Rule::in(['overview', 'sessions'])],
            'q'         => ['nullable', 'string', 'max:255'],
            'sprint_id' => [
                'nullable',
                function (string $attribute, mixed $value, \Closure $fail): void {
                    if ($value === null || $value === 'none') {
                        return;
                    }
                    if (!is_numeric($value) || !Sprint::query()->whereKey($value)->exists()) {
                        $fail('The selected sprint filter is invalid.');
                    }
                },
            ],
            'billable'  => ['nullable', 'string', Rule::in(['yes', 'no'])],
            'sort'      => ['required', 'string', Rule::in(['ended_at', 'started_at', 'task', 'sprint', 'category', 'duration', 'billable'])],
            'direction' => ['required', 'string', Rule::in(['asc', 'desc'])],
            'per_page'  => ['required', 'integer', Rule::in([10, 15, 25, 50])],
        ];
    }

    /**
     * @return array{
     *     q: string,
     *     sprint_id: int|string|null,
     *     billable: string|null,
     *     sort: string,
     *     direction: string,
     *     per_page: int
     * }
     */
    public function sessionsTableState(): array
    {
        /** @var array{
         *     q?: string|null,
         *     sprint_id?: int|string|null,
         *     billable?: string|null,
         *     sort: string,
         *     direction: string,
         *     per_page: int
         * } $v */
        $v = $this->validated();

        return [
            'q'         => isset($v['q']) ? trim((string) $v['q']) : '',
            'sprint_id' => $v['sprint_id'] ?? null,
            'billable'  => $v['billable'] ?? null,
            'sort'      => $v['sort'],
            'direction' => $v['direction'],
            'per_page'  => $v['per_page'],
        ];
    }
}
