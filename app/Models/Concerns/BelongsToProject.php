<?php

namespace App\Models\Concerns;

use App\Models\Project;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

trait BelongsToProject
{
    use HasRelations;

    /**
     * @return BelongsTo<Project, $this>
     */
    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    public function getProject(): Project
    {
        return $this->getRelation(Project::class);
    }
}
