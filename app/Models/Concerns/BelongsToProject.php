<?php

namespace App\Models\Concerns;

use App\Models\Project;

trait BelongsToProject
{
    use HasRelations;

    public function project()
    {
        if ($this->parent) {
            return $this->parent->project();
        }

        return $this->belongsTo(Project::class);
    }

    public function getProject()
    {
        return $this->getRelation(Project::class);
    }
}
