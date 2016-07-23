<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Client extends Model
{
    /**
     * The fields that may be mass assigned
     *
     * @var array $fillable
     **/
    protected $fillable = [
        'name',
        'email'
    ];
}
