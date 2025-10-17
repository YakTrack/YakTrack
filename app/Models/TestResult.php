<?php

namespace App\Models;

use App\TestResultStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class TestResult extends Model
{
    /** @use HasFactory<\Database\Factories\TestResultFactory> */
    use HasFactory;

    protected $fillable = [
        'test_run_id',
        'acceptance_criteria_id',
        'acceptance_criteria_version_id',
        'status',
        'notes',
    ];

    protected $casts = [
        'status' => TestResultStatus::class,
    ];

    /**
     * @return BelongsTo<TestRun, $this>
     */
    public function testRun(): BelongsTo
    {
        return $this->belongsTo(TestRun::class);
    }

    /**
     * @return BelongsTo<AcceptanceCriteria, $this>
     */
    public function acceptanceCriteria(): BelongsTo
    {
        return $this->belongsTo(AcceptanceCriteria::class);
    }

    /**
     * @return BelongsTo<AcceptanceCriteriaVersion, $this>
     */
    public function acceptanceCriteriaVersion(): BelongsTo
    {
        return $this->belongsTo(AcceptanceCriteriaVersion::class);
    }

    /**
     * @return HasMany<TestResultEvidence, $this>
     */
    public function evidence(): HasMany
    {
        return $this->hasMany(TestResultEvidence::class)->orderBy('sort_order');
    }

    public function isPassed(): bool
    {
        return $this->status === TestResultStatus::Passed;
    }

    public function isFailed(): bool
    {
        return $this->status === TestResultStatus::Failed;
    }

    public function isSkipped(): bool
    {
        return $this->status === TestResultStatus::Skipped;
    }

    public function isBlocked(): bool
    {
        return $this->status === TestResultStatus::Blocked;
    }

    public function getEvidenceCountAttribute(): int
    {
        return $this->evidence()->count();
    }

    public function getImageEvidenceAttribute()
    {
        return $this->evidence()->where('type', 'image')->get();
    }

    public function getTextEvidenceAttribute()
    {
        return $this->evidence()->where('type', 'text')->get();
    }
}