<?php

namespace App\Models;

use App\EvidenceType;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TestResultEvidence extends Model
{
    /** @use HasFactory<\Database\Factories\TestResultEvidenceFactory> */
    use HasFactory;

    protected $fillable = [
        'test_result_id',
        'type',
        'file_path',
        'content',
        'sort_order',
    ];

    protected $casts = [
        'type' => EvidenceType::class,
    ];

    /**
     * @return BelongsTo<TestResult, $this>
     */
    public function testResult(): BelongsTo
    {
        return $this->belongsTo(TestResult::class);
    }

    public function isImage(): bool
    {
        return $this->type === EvidenceType::Image;
    }

    public function isText(): bool
    {
        return $this->type === EvidenceType::Text;
    }

    public function getFileUrlAttribute(): ?string
    {
        if ($this->isImage() && $this->file_path) {
            return asset('storage/test-evidence/' . basename($this->file_path));
        }

        return null;
    }
}