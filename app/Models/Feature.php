<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Feature extends Model
{
    /** @use HasFactory<\Database\Factories\FeatureFactory> */
    use HasFactory;

    protected $fillable = [
        'project_id',
        'name',
        'description',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    /**
     * @return BelongsTo<Project, $this>
     */
    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    /**
     * @return HasMany<AcceptanceCriteria, $this>
     */
    public function acceptanceCriteria(): HasMany
    {
        return $this->hasMany(AcceptanceCriteria::class);
    }

    /**
     * Get acceptance criteria count for this feature.
     */
    public function getAcceptanceCriteriaCountAttribute(): int
    {
        return $this->acceptanceCriteria()->where('is_active', true)->count();
    }

    /**
     * Find or create a feature by name and project.
     */
    public static function findOrCreateByName(string $name, int $projectId): self
    {
        return static::firstOrCreate(
            [
                'project_id' => $projectId,
                'name'       => $name,
            ],
            [
                'is_active' => true,
            ]
        );
    }

    /**
     * Get features for a project.
     */
    public static function getForProject(int $projectId): \Illuminate\Database\Eloquent\Collection
    {
        return static::where('project_id', $projectId)
            ->where('is_active', true)
            ->orderBy('name')
            ->get();
    }
}
