<?php

namespace App\Models\Collections;

use Illuminate\Database\Eloquent\Collection as EloquentCollection;

class SessionCollection extends EloquentCollection
{
    public function totalDurationInSeconds()
    {
        return $this->sum(function ($session) {
            return $session->durationInSeconds;
        });
    }
}
