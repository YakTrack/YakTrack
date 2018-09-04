<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Task extends Model
{
    protected $guarded = [];

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
