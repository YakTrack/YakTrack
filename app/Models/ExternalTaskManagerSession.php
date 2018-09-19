<?php

namespace App\Models;

use App\Session;
use Illuminate\Database\Eloquent\Model;

class ExternalTaskManagerSession extends Model
{
    protected $guarded = [];

    public function session()
    {
        return $this->belongsTo(Session::class);
    }
}
