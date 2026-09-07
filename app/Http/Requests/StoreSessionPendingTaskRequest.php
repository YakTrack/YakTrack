<?php

namespace App\Http\Requests;

use App\Models\Task;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Validator;

class StoreSessionPendingTaskRequest extends FormRequest
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
            'task_id' => ['required', 'integer', 'exists:tasks,id'],
        ];
    }

    /**
     * Mirror the active-task rule used when building the session form task list:
     * a task can only be linked when its project is not archived and its status
     * is either unset or not closed.
     */
    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $validator): void {
            $taskId = $this->input('task_id');

            if (!$taskId || $validator->errors()->has('task_id')) {
                return;
            }

            $isLinkable = Task::query()
                ->whereKey($taskId)
                ->whereHas('project', fn (Builder $project): Builder => $project->whereNull('archived_at'))
                ->where(function (Builder $task): void {
                    $task->whereNull('status_id')
                        ->orWhereHas('taskStatus', fn (Builder $status): Builder => $status->where('is_closed', false));
                })
                ->exists();

            if (!$isLinkable) {
                $validator->errors()->add('task_id', 'This task cannot be linked because its project is archived or its status is closed.');
            }
        });
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'task_id.required' => 'Select a task to link.',
            'task_id.exists'   => 'The selected task does not exist.',
        ];
    }
}
