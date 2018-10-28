<?php

namespace App\Models;

use App\Models\Collections\ProjectCollection;
use Illuminate\Database\Eloquent\Model;

class Project extends Model
{
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
    public function client()
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
    public function sprints()
    {
        return $this->hasMany('App\Models\Sprint');
    }

    /**
     * The tasks that belong to the project.
     **/
    public function tasks()
    {
        return $this->hasMany(Task::class);
    }
}
