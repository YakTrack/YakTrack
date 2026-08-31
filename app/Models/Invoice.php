<?php

namespace App\Models;

use App\Models\Concerns\BelongsToClient;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Invoice extends Model
{
    use BelongsToClient;
    /** @use HasFactory<\Database\Factories\InvoiceFactory> */
    use HasFactory;

    protected $guarded = [];

    /**
     * Scope to the focused client. No-op when no client is focused.
     *
     * @param Builder<\App\Models\Invoice> $query
     *
     * @return Builder<\App\Models\Invoice>
     */
    public function scopeForFocusedClient(Builder $query, ?int $clientId): Builder
    {
        return $query->when($clientId, fn (Builder $query): Builder => $query->where('client_id', $clientId));
    }

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
     * @return HasMany<Session, $this>
     */
    public function sessions(): HasMany
    {
        return $this->hasMany(Session::class);
    }
}
