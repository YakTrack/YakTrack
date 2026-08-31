<?php

namespace App\Models;

use App\Models\Collections\TaskCollection;
use App\Models\Concerns\BelongsToProject;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Task extends Model
{
    use BelongsToProject;
    /** @use HasFactory<\Database\Factories\TaskFactory> */
    use HasFactory;

    protected $guarded = [];

    protected $appends = [
        'shortName',
    ];

    /**
     * Create a new Eloquent Collection instance.
     *
     * @param array<int, \App\Models\Task> $models
     *
     * @return \App\Models\Collections\TaskCollection
     *
     * @phpstan-return \App\Models\Collections\TaskCollection
     */
    public function newCollection(array $models = []): TaskCollection
    {
        return new TaskCollection($models);
    }

    /**
     * @return HasMany<Session, $this>
     */
    public function sessions(): HasMany
    {
        return $this->hasMany(Session::class);
    }

    /**
     * Scope to the focused client via the parent project. No-op when no client is focused.
     *
     * @param Builder<\App\Models\Task> $query
     *
     * @return Builder<\App\Models\Task>
     */
    public function scopeForFocusedClient(Builder $query, ?int $clientId): Builder
    {
        return $query->when($clientId, fn (Builder $query): Builder => $query->whereHas('project', fn (Builder $project): Builder => $project->where('client_id', $clientId)));
    }

    /**
     * @return BelongsTo<TaskStatus, $this>
     */
    public function taskStatus(): BelongsTo
    {
        return $this->belongsTo(TaskStatus::class, 'status_id');
    }

    /**
     * @return BelongsToMany<AcceptanceCriteria, $this>
     */
    public function acceptanceCriteria(): BelongsToMany
    {
        return $this->belongsToMany(AcceptanceCriteria::class, 'acceptance_criteria_task');
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

    public function getShortNameAttribute(): string
    {
        return substr($this->name, 0, $shortNameLength = 50).(strlen($this->name) > $shortNameLength ? '...' : '');
    }
}
