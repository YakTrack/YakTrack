<?php

namespace App\Models;

use App\Models\Collections\ProjectCollection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;

class Project extends Model
{
    /** @use HasFactory<\Database\Factories\ProjectFactory> */
    use HasFactory;
    protected $fillable = [
        'name',
        'description',
        'task_code_prefix',
        'client_id',
        'archived_at',
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
     * The sprints that include this project.
     *
     * @return BelongsToMany<Sprint, $this>
     */
    public function sprints(): BelongsToMany
    {
        return $this->belongsToMany(Sprint::class, 'project_sprint');
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

    /**
     * Archive the project.
     */
    public function archive(): void
    {
        $this->update(['archived_at' => now()]);
    }

    /**
     * Unarchive the project.
     */
    public function unarchive(): void
    {
        $this->update(['archived_at' => null]);
    }

    /**
     * Check if the project is archived.
     */
    public function isArchived(): bool
    {
        return !is_null($this->archived_at);
    }

    /**
     * Get the archived status attribute.
     */
    public function getIsArchivedAttribute(): bool
    {
        return $this->isArchived();
    }

    /**
     * Scope to get only archived projects.
     */
    public function scopeArchived($query)
    {
        return $query->whereNotNull('archived_at');
    }

    /**
     * Scope to get only non-archived projects.
     */
    public function scopeNotArchived($query)
    {
        return $query->whereNull('archived_at');
    }

    /**
     * Get the next task code for this project.
     */
    public function getNextTaskCode(): ?string
    {
        if (!$this->task_code_prefix) {
            return null;
        }

        // Get all tasks for this project
        $tasks = $this->tasks()->pluck('name');

        // Extract numbers from task names that match the pattern
        $pattern = '/^'.preg_quote($this->task_code_prefix, '/').'-(\d+):/';
        $numbers = [];

        foreach ($tasks as $taskName) {
            if (preg_match($pattern, $taskName, $matches)) {
                $numbers[] = (int) $matches[1];
            }
        }

        // Get the next number (or start at 1 if no tasks exist)
        $nextNumber = empty($numbers) ? 1 : max($numbers) + 1;

        // Format with leading zeros (4 digits)
        return sprintf('%s-%04d: ', $this->task_code_prefix, $nextNumber);
    }
}
