<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class AcceptanceCriteria extends Model
{
    /** @use HasFactory<\Database\Factories\AcceptanceCriteriaFactory> */
    use HasFactory;

    protected $table = 'acceptance_criteria';

    protected $fillable = [
        'project_id',
        'code',
        'name',
        'description',
        'feature_id',
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
     * @return BelongsTo<Feature, $this>
     */
    public function feature(): BelongsTo
    {
        return $this->belongsTo(Feature::class);
    }

    /**
     * @return BelongsToMany<Task, $this>
     */
    public function tasks(): BelongsToMany
    {
        return $this->belongsToMany(Task::class, 'acceptance_criteria_task');
    }

    /**
     * @return HasMany<AcceptanceCriteriaVersion, $this>
     */
    public function versions(): HasMany
    {
        return $this->hasMany(AcceptanceCriteriaVersion::class)->orderBy('version_number', 'desc');
    }

    /**
     * @return HasMany<TestResult, $this>
     */
    public function testResults(): HasMany
    {
        return $this->hasMany(TestResult::class);
    }

    public function createVersion(array $data, int $userId): AcceptanceCriteriaVersion
    {
        $maxVersion = $this->versions()->max('version_number');
        $versionNumber = $maxVersion ? $maxVersion + 1 : 1;

        return $this->versions()->create([
            'code'               => $data['code'] ?? $this->code,
            'name'               => $data['name'] ?? $this->name,
            'description'        => $data['description'] ?? $this->description,
            'feature_id'         => $data['feature_id'] ?? $this->feature_id,
            'version_number'     => $versionNumber,
            'changed_at'         => now(),
            'changed_by_user_id' => $userId,
        ]);
    }

    public function updateWithVersion(array $data, int $userId): bool
    {
        $this->createVersion($data, $userId);

        return $this->update($data);
    }

    public function getCurrentVersion(): ?AcceptanceCriteriaVersion
    {
        return $this->versions()->first();
    }

    public function getVersionCountAttribute(): int
    {
        return $this->versions()->count();
    }

    public function getLinkedTasksCountAttribute(): int
    {
        return $this->tasks()->count();
    }

    public function getLatestTestResultAttribute(): ?TestResult
    {
        return $this->testResults()->latest()->first();
    }

    /**
     * Get acceptance criteria grouped by feature.
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public static function getGroupedByFeature(?int $projectId = null): \Illuminate\Database\Eloquent\Collection
    {
        $query = static::with(['project', 'feature', 'versions', 'tasks'])
            ->where('is_active', true);

        if ($projectId) {
            $query->where('project_id', $projectId);
        }

        return $query->orderBy('feature_id')
            ->orderBy('name')
            ->get()
            ->groupBy(function ($item) {
                return $item->feature ? $item->feature->name : 'No Feature';
            });
    }

    /**
     * Get features for a project.
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public static function getFeaturesForProject(int $projectId): \Illuminate\Database\Eloquent\Collection
    {
        return Feature::getForProject($projectId);
    }
}
