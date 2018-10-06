<?php

namespace App\Models;

use App\Models\Concerns\BelongsToProject;
use Illuminate\Database\Eloquent\Model;

class Task extends Model
{
    use BelongsToProject;

    protected $guarded = [];

    public function sessions()
    {
        return $this->hasMany(Session::class);
    }

    public function getClient()
    {
        return $this->getRelation(Client::class);
    }

    public function getProject()
    {
        return $this->getRelation(Project::class);
    }

    public function getSprint()
    {
        return $this->getRelation(Sprint::class);
    }

    public function getParent()
    {
        return $this->parent ?? new self();
    }

    public function parent()
    {
        return $this->belongsTo(self::class, 'parent_id');
    }

    public function getShortNameAttribute()
    {
        return substr($this->name, 0, $shortNameLength = 50).(strlen($this->name) > $shortNameLength ? '...' : '');
    }
}
