<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
class Sprint extends Model
{
    use HasFactory;

    protected $guarded = [];

    /**
     * The relationship to the project which this sprint belongs to.
     **/
    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    public function sessions(): HasMany
    {
        return $this->hasMany(Session::class);
    }

    public function scopeOpen($query)
    {
        return $query->where('is_open', 1);
    }
}
