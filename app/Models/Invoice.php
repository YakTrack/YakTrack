<?php

namespace App\Models;

use App\Models\Concerns\BelongsToClient;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
class Invoice extends Model
{
    use BelongsToClient;
    use HasFactory;

    protected $guarded = [];

    protected $appends = [
        'amountForHumans',
    ];

    public function getAmountForHumansAttribute(): string
    {
        return number_format($this->amount / 100, 2);
    }

    public function getTotalDurationForHumansAttribute(): string
    {
        return $this->sessions->totalDurationForHumans();
    }

    /**
     * @return HasMany<Session>
     */
    public function sessions(): HasMany
    {
        return $this->hasMany(Session::class);
    }
}
