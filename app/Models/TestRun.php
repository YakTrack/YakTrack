<?php

namespace App\Models;

use App\TestResultStatus;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class TestRun extends Model
{
    /** @use HasFactory<\Database\Factories\TestRunFactory> */
    use HasFactory;

    protected $fillable = [
        'project_id',
        'name',
        'description',
        'executed_at',
        'executed_by_user_id',
    ];

    protected $casts = [
        'executed_at' => 'datetime',
    ];

    /**
     * @return BelongsTo<Project, $this>
     */
    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    /**
     * @return BelongsTo<User, $this>
     */
    public function executedByUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'executed_by_user_id');
    }

    /**
     * @return HasMany<TestResult, $this>
     */
    public function testResults(): HasMany
    {
        return $this->hasMany(TestResult::class);
    }

    /**
     * Scope to the focused client via the parent project. No-op when no client is focused.
     *
     * @param Builder<\App\Models\TestRun> $query
     *
     * @return Builder<\App\Models\TestRun>
     */
    public function scopeForFocusedClient(Builder $query, ?int $clientId): Builder
    {
        return $query->when($clientId, fn (Builder $query): Builder => $query->whereHas('project', fn (Builder $project): Builder => $project->where('client_id', $clientId)));
    }

    public function getSummaryStatistics(): array
    {
        $total = $this->testResults()->count();
        $pending = $this->testResults()->where('status', TestResultStatus::Pending)->count();
        $passed = $this->testResults()->where('status', TestResultStatus::Passed)->count();
        $failed = $this->testResults()->where('status', TestResultStatus::Failed)->count();
        $skipped = $this->testResults()->where('status', TestResultStatus::Skipped)->count();
        $blocked = $this->testResults()->where('status', TestResultStatus::Blocked)->count();

        return [
            'total'           => $total,
            'pending'         => $pending,
            'passed'          => $passed,
            'failed'          => $failed,
            'skipped'         => $skipped,
            'blocked'         => $blocked,
            'pass_rate'       => $total > 0 ? round(($passed / $total) * 100, 1) : 0,
            'completion_rate' => $total > 0 ? round((($passed + $failed) / $total) * 100, 1) : 0,
        ];
    }

    public function isComplete(): bool
    {
        $total = $this->testResults()->count();
        $completed = $this->testResults()
            ->whereIn('status', [TestResultStatus::Passed, TestResultStatus::Failed])
            ->count();

        return $total > 0 && $completed === $total;
    }

    public function getPassRateAttribute(): float
    {
        return $this->getSummaryStatistics()['pass_rate'];
    }

    public function getCompletionRateAttribute(): float
    {
        return $this->getSummaryStatistics()['completion_rate'];
    }
}
