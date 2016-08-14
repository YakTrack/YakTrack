<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Sprint extends Model
{
    protected $fillable = ['name', 'project_id'];

    /**
     * The relationship to the project which this sprint belongs to
     **/
    public function project()
    {
        return $this->belongsTo('App\Project');
    }
}
