<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Sprint extends Model
{
    protected $fillable = [
        'project_id'
    ];

    /**
     * Returns true if sprint is linked to project
     *
     * @return bool
     **/
    public function hasProject()
    {
        return !is_null($this->project);
    }
}
