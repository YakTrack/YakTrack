<?php

namespace App\Models;

use App\Models\Concerns\BelongsToClient;

class Invoice extends Model
{
    use BelongsToClient;

    protected $guarded = [];

    protected $appends = [
        'amountForHumans',
        'totalDurationForHumans',
    ];

    public function getAmountForHumansAttribute()
    {
        return number_format($this->amount / 100, 2);
    }

    public function getTotalDurationForHumansAttribute()
    {
        return $this->sessions->totalDurationForHumans();
    }

    public function sessions()
    {
        return $this->hasMany(Session::class);
    }
}
