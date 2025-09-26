<?php

namespace App\Models;

use App\Models\Concerns\BelongsToClient;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Invoice extends Model
{
    use BelongsToClient, HasFactory;

    protected $guarded = [];

    protected $appends = [
        'amountForHumans',
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
