<?php

namespace App\Models\Collections;

use Illuminate\Database\Eloquent\Collection as EloquentCollection;

class TaskCollection extends EloquentCollection
{
    public function sessions()
    {
        return new SessionCollection($this->flatMap(function ($task) {
            return $task->sessions;
        }));
    }
}
