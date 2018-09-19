<?php

namespace App\Models;

use App\Session;
use Illuminate\Database\Eloquent\Model;

class ThirdPartyApplicationSession extends Model
{
    protected $guarded = [];

    public function session()
    {
        return $this->belongsTo(Session::class);
    }
}
