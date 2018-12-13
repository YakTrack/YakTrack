<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Sprint extends Model
{
    protected $guarded = [];

    /**
     * The relationship to the project which this sprint belongs to.
     **/
    public function project()
    {
        return $this->belongsTo(Project::class);
    }

    public function sessions()
    {
        return $this->hasMany(Session::class);
    }

    public function scopeOpen($query)
    {
        return $query->where('is_open', 1);
    }
}
