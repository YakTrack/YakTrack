<?php

namespace App\Models\Collections;

use App\Models\Collections\TaskCollection;
use Illuminate\Database\Eloquent\Collection as EloquentCollection;

/**
 * @extends EloquentCollection<int, \App\Models\Project>
 */
class ProjectCollection extends EloquentCollection
{
    public function sessionsThisWeek(): SessionCollection
    {
        return $this->sessions()->thisWeek();
    }

    public function sessions(): SessionCollection
    {
        return $this->tasks()->sessions();
    }

    public function tasks(): TaskCollection
    {
        return new TaskCollection($this->flatMap(function ($project) {
            return $project->tasks;
        }));
    }
}
