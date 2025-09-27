<?php

namespace App\Models\Collections;

use Illuminate\Database\Eloquent\Collection as EloquentCollection;

/**
 * @extends EloquentCollection<int, \App\Models\Task>
 */
class TaskCollection extends EloquentCollection
{
    public function sessions(): SessionCollection
    {
        return new SessionCollection($this->flatMap(function ($task) {
            return $task->sessions;
        }));
    }
}
