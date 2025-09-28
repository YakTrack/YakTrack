<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Sprint extends Model
{
    /** @use HasFactory<\Database\Factories\SprintFactory> */
    use HasFactory;

    protected $guarded = [];

    /**
     * The relationship to the project which this sprint belongs to.
     **/
    /**
     * @return BelongsTo<Project, $this>
     */
    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
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
}
