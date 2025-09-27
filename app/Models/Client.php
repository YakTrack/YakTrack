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

    /**
     * @return HasMany<Invoice>
     */
    public function invoices(): HasMany
    {
        return $this->hasMany(Invoice::class);
    }

    /**
     * @return HasMany<Project>
     */
    public function projects(): HasMany
    {
        return $this->hasMany(Project::class);
    }

    /**
     * @return HasManyThrough<Sprint>
     */
    public function sprints(): HasManyThrough
    {
        return $this->hasManyThrough(Sprint::class, Project::class);
    }

    /**
     * @return \App\Models\Collections\SessionCollection
     */
    public function getSessionsThisWeekAttribute(): \App\Models\Collections\SessionCollection
    {
        return $this->projects->sessionsThisWeek();
    }

    /**
     * @return \Illuminate\Database\Eloquent\Collection<int, \App\Models\Sprint>
     */
    public function getOpenSprintsAttribute(): \Illuminate\Database\Eloquent\Collection
    {
        return $this->sprints()->open()->with('sessions')->get();
    }

    /**
     * @return HasMany<ClientUser>
     */
    public function clientUsers(): HasMany
    {
        return $this->hasMany(ClientUser::class);
    }
}
