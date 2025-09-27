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
     * @param array<int, \App\Models\Task> $models
     */
    public function newCollection(array $models = []): TaskCollection
    {
        return new TaskCollection($models);
    }

    /**
     * @return HasMany<Session>
     */
    public function sessions(): HasMany
    {
        return $this->hasMany(Session::class);
    }

    /**
     * @return BelongsTo<Task, Task>
     */
    public function parent(): BelongsTo
    {
        return $this->belongsTo(self::class, 'parent_id');
    }

    /**
     * @return BelongsTo<TaskStatus, Task>
     */
    public function taskStatus(): BelongsTo
    {
        return $this->belongsTo(TaskStatus::class, 'status_id');
    }

    public function openSprint(): Sprint
    {
        return $this->getProject()->sprints()->open()->orderBy('id', 'desc')->first() ?? new Sprint();
    }

    public function getClient(): Client
    {
        return $this->getProject()->getClient();
    }

    public function getProject(): Project
    {
        return $this->getRelation(Project::class);
    }

    public function getParent(): Task
    {
        return $this->parent ?? new Task();
    }

    public function getShortNameAttribute(): string
    {
        return substr($this->name, 0, $shortNameLength = 50).(strlen($this->name) > $shortNameLength ? '...' : '');
    }
}
