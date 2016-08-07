<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

use App\Client;

class Project extends Model
{
    protected $fillable = [
        'name',
        'description',
        'client_id'
    ];

    /**
     * The client that the project belongs to
     **/
    public function client()
    {
        return $this->belongsTo('App\Client');
    }

    /**
     * Returns the client object that the project belongs to if one exists or
     * a blank client object if none exists.
     *
     * @return \App\Client
     **/
    public function getClient()
    {
        if (is_null($this->client)) {
            return new Client;
        }

        return $this->client;
    }
}
