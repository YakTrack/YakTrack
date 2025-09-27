<?php

namespace App\Models;

use App\Models\Collections\ProjectCollection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
class Project extends Model
{
    use HasFactory;
    protected $fillable = [
        'name',
        'description',
        'client_id',
    ];

    /**
     * Create a new Eloquent Collection instance.
     *
     * @param array $models
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function newCollection(array $models = [])
    {
        return new ProjectCollection($models);
    }

    /**
     * The client that the project belongs to.
     **/
    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }

    /**
     * Returns the client object that the project belongs to if one exists or
     * a blank client object if none exists.
     *
     * @return \App\Models\Client
     **/
    public function getClient()
    {
        if (is_null($this->client)) {
            return new Client();
        }

        return $this->client;
    }

    /**
     * The sprints that belong to the project.
     **/
    public function sprints(): HasMany
    {
        return $this->hasMany(Sprint::class);
    }

    /**
     * The tasks that belong to the project.
     **/
    public function tasks(): HasMany
    {
        return $this->hasMany(Task::class);
    }

    /**
     * The task statuses that belong to the project.
     **/
    public function taskStatuses(): HasMany
    {
        return $this->hasMany(TaskStatus::class)->orderBy('sort_order');
    }

    /**
     * The sessions that belong to the project through tasks.
     **/
    public function sessions(): HasManyThrough
    {
        return $this->hasManyThrough(Session::class, Task::class);
    }

    public function isDeletable()
    {
        if ($this->sprints->count() > 0) {
            return false;
        }

        if ($this->tasks->count() > 0) {
            return false;
        }

        return true;
    }

    public function getIsDeletableAttribute()
    {
        return $this->isDeletable();
    }
}
