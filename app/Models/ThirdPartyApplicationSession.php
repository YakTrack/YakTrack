<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ThirdPartyApplicationSession extends Model
{
    protected $guarded = [];

    public function session()
    {
        return $this->belongsTo(Session::class);
    }

    public function isForThirdPartyApplication(ThirdPartyApplication $app)
    {
        return $this->third_party_application_id === $app->id;
    }
}
