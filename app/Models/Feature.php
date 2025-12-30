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
        'code',
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
    public static function findOrCreateByName(string $name, int $projectId, ?string $code = null): self
    {
        $feature = static::firstOrCreate(
            [
                'project_id' => $projectId,
                'name'       => $name,
            ],
            [
                'code'      => null, // Don't set code on create to avoid constraint violations
                'is_active' => true,
            ]
        );

        // Update code if provided and feature doesn't have one
        // Check if code is already used by another feature in the same project
        if ($code && !$feature->code) {
            $codeExists = static::where('project_id', $projectId)
                ->where('code', $code)
                ->where('id', '!=', $feature->id)
                ->exists();

            if (!$codeExists) {
                try {
                    $feature->update(['code' => $code]);
                } catch (\Illuminate\Database\QueryException $e) {
                    // Handle race condition where code was assigned between check and update
                    // Feature will remain without code in this case
                    if ($e->getCode() !== '23000') {
                        throw $e;
                    }
                }
            }
        }

        return $feature;
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
