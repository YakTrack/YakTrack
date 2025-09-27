<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
class Client extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function invoices(): HasMany
    {
        return $this->hasMany(Invoice::class);
    }

    public function projects(): HasMany
    {
        return $this->hasMany(Project::class);
    }

    public function sprints(): HasManyThrough
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

    public function clientUsers(): HasMany
    {
        return $this->hasMany(ClientUser::class);
    }
}
