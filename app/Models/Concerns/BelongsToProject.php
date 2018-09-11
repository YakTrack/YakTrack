<?php

namespace App\Models\Concerns;

use App\Project;

trait BelongsToProject
{
    use HasRelations;

    public function project()
    {
        return $this->belongsTo(Project::class);
    }

    public function getProject()
    {
        return $this->getRelation(Project::class);
    }
}
