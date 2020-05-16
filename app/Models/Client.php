<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Client extends Model
{
    protected $guarded = [];

    public function invoices()
    {
        return $this->hasMany(Invoice::class);
    }

    public function projects()
    {
        return $this->hasMany(Project::class);
    }

    public function sprints()
    {
        return $this->hasManyThrough(Sprint::class, Project::class);
    }

    public function getSessionsThisWeekAttribute()
    {
        return $this->projects->sessionsThisWeek()->values();
    }

    public function getOpenSprintsAttribute()
    {
        return $this->sprints()->open()->with('sessions')->get();
    }
}
