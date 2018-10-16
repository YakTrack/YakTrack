<?php

namespace App\Models\Collections;

use Illuminate\Database\Eloquent\Collection as EloquentCollection;

class ProjectCollection extends EloquentCollection
{
    public function sessionsThisWeek()
    {
        return $this->sessions()->thisWeek();
    }

    public function sessions()
    {
        return $this->tasks()->sessions();
    }

    public function tasks()
    {
        return new TaskCollection($this->flatMap(function ($project) {
            return $project->tasks;
        }));
    }
}
