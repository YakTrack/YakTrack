<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AcceptanceCriteriaVersion extends Model
{
    /** @use HasFactory<\Database\Factories\AcceptanceCriteriaVersionFactory> */
    use HasFactory;

    protected $fillable = [
        'acceptance_criteria_id',
        'code',
        'name',
        'description',
        'feature_id',
        'version_number',
        'changed_at',
        'changed_by_user_id',
    ];

    protected $casts = [
        'changed_at' => 'datetime',
    ];

    /**
     * @return BelongsTo<AcceptanceCriteria, $this>
     */
    public function acceptanceCriteria(): BelongsTo
    {
        return $this->belongsTo(AcceptanceCriteria::class);
    }

    /**
     * @return BelongsTo<Feature, $this>
     */
    public function feature(): BelongsTo
    {
        return $this->belongsTo(Feature::class);
    }

    /**
     * @return BelongsTo<User, $this>
     */
    public function changedByUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'changed_by_user_id');
    }
}