<?php

namespace App\Models;

use App\Models\Collections\ProjectCollection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;

class Project extends Model
{
    /** @use HasFactory<\Database\Factories\ProjectFactory> */
    use HasFactory;
    protected $fillable = [
        'name',
        'description',
        'client_id',
    ];

    /**
     * Create a new Eloquent Collection instance.
     *
     * @param array<int, \App\Models\Project> $models
     *
     * @return \App\Models\Collections\ProjectCollection
     *
     * @phpstan-return \App\Models\Collections\ProjectCollection
     */
    public function newCollection(array $models = []): ProjectCollection
    {
        return new ProjectCollection($models);
    }

    /**
     * The client that the project belongs to.
     **/
    /**
     * @return BelongsTo<Client, $this>
     */
    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }

    /**
     * Returns the client object that the project belongs to if one exists or
     * a blank client object if none exists.
     *
     * @return \App\Models\Client
     **/
    public function getClient(): Client
    {
        if (is_null($this->client)) {
            return new Client();
        }

        return $this->client;
    }

    /**
     * The sprints that belong to the project.
     **/
    /**
     * @return HasMany<Sprint, $this>
     */
    public function sprints(): HasMany
    {
        return $this->hasMany(Sprint::class);
    }

    /**
     * The tasks that belong to the project.
     **/
    /**
     * @return HasMany<Task, $this>
     */
    public function tasks(): HasMany
    {
        return $this->hasMany(Task::class);
    }

    /**
     * The task statuses that belong to the project.
     **/
    /**
     * @return HasMany<TaskStatus, $this>
     */
    public function taskStatuses(): HasMany
    {
        return $this->hasMany(TaskStatus::class)->orderBy('sort_order');
    }

    /**
     * The sessions that belong to the project through tasks.
     **/
    /**
     * @return HasManyThrough<Session, Task, $this>
     */
    public function sessions(): HasManyThrough
    {
        return $this->hasManyThrough(Session::class, Task::class);
    }

    /**
     * The acceptance criteria that belong to the project.
     **/
    /**
     * @return HasMany<AcceptanceCriteria, $this>
     */
    public function acceptanceCriteria(): HasMany
    {
        return $this->hasMany(AcceptanceCriteria::class)->where('is_active', true);
    }

    /**
     * The test runs that belong to the project.
     **/
    /**
     * @return HasMany<TestRun, $this>
     */
    public function testRuns(): HasMany
    {
        return $this->hasMany(TestRun::class)->orderBy('executed_at', 'desc');
    }

    public function isDeletable(): bool
    {
        if ($this->sprints->count() > 0) {
            return false;
        }

        if ($this->tasks->count() > 0) {
            return false;
        }

        if ($this->acceptanceCriteria->count() > 0) {
            return false;
        }

        if ($this->testRuns->count() > 0) {
            return false;
        }

        return true;
    }

    public function getIsDeletableAttribute(): bool
    {
        return $this->isDeletable();
    }
}
