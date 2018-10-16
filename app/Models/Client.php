<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Client extends Model
{
    protected $guarded = [];

    public function getSessionsThisWeekAttribute()
    {
        return $this->projects->sessionsThisWeek();
    }

    public function projects()
    {
        return $this->hasMany(Project::class);
    }
}
