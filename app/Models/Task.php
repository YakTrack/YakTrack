<?php

namespace App\Models;

use App\Models\Collections\TaskCollection;
use App\Models\Concerns\BelongsToProject;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
class Task extends Model
{
    use BelongsToProject;
    use HasFactory;

    protected $guarded = [];

    protected $appends = [
        'shortName',
    ];

    /**
     * Create a new Eloquent Collection instance.
     *
     * @param array $models
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function newCollection(array $models = [])
    {
        return new TaskCollection($models);
    }

    public function sessions(): HasMany
    {
        return $this->hasMany(Session::class);
    }

    public function parent(): BelongsTo
    {
        return $this->belongsTo(self::class, 'parent_id');
    }

    public function taskStatus(): BelongsTo
    {
        return $this->belongsTo(TaskStatus::class, 'status_id');
    }

    public function openSprint()
    {
        return $this->getProject()->sprints()->open()->orderBy('id', 'desc')->first() ?? new Sprint();
    }

    public function getClient()
    {
        return $this->getProject()->getClient();
    }

    public function getProject()
    {
        return $this->getRelation(Project::class);
    }

    public function getParent()
    {
        return $this->parent ?? new self();
    }

    public function getShortNameAttribute()
    {
        return substr($this->name, 0, $shortNameLength = 50).(strlen($this->name) > $shortNameLength ? '...' : '');
    }
}
