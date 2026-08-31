<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Sprint extends Model
{
    /** @use HasFactory<\Database\Factories\SprintFactory> */
    use HasFactory;

    protected $guarded = [];

    /**
     * The projects included in this sprint.
     *
     * @return BelongsToMany<Project, $this>
     */
    public function projects(): BelongsToMany
    {
        return $this->belongsToMany(Project::class, 'project_sprint');
    }

    /**
     * @return HasMany<Session, $this>
     */
    public function sessions(): HasMany
    {
        return $this->hasMany(Session::class);
    }

    /**
     * @param \Illuminate\Database\Eloquent\Builder<\App\Models\Sprint> $query
     *
     * @return \Illuminate\Database\Eloquent\Builder<\App\Models\Sprint>
     */
    public function scopeOpen(\Illuminate\Database\Eloquent\Builder $query): \Illuminate\Database\Eloquent\Builder
    {
        return $query->where('is_open', 1);
    }

    /**
     * Scope to sprints touching the focused client's projects. No-op when no client is focused.
     *
     * @param Builder<\App\Models\Sprint> $query
     *
     * @return Builder<\App\Models\Sprint>
     */
    public function scopeForFocusedClient(Builder $query, ?int $clientId): Builder
    {
        return $query->when($clientId, fn (Builder $query): Builder => $query->whereHas('projects', fn (Builder $project): Builder => $project->where('client_id', $clientId)));
    }
}
