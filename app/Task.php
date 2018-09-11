<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\Models\Concerns\BelongstoProject;

class Task extends Model
{
    use BelongsToProject;

    protected $guarded = [];

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
        return $this->parent ?? new Task;
    }

    public function parent()
    {
        return $this->belongsTo(Task::class, 'parent_id');
    }

    public function getShortNameAttribute()
    {
        return substr($this->name, 0, $shortNameLength = 50).(strlen($this->name) > $shortNameLength ? '...' : '');
    }
}
