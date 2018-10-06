<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Client extends Model
{
    /**
     * The fields that may be mass assigned.
     *
     * @var array
     **/
    protected $fillable = [
        'name',
        'email',
    ];
}
