<?php

namespace App\Models;

use App\Models\Collections\TaskCollection;
use App\Models\Concerns\BelongsToProject;
use Illuminate\Database\Eloquent\Model;

class Task extends Model
{
    use BelongsToProject;

    protected $guarded = [];

    protected $appends = [
        'shortName',
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
        return new TaskCollection($models);
    }

    public function sessions()
    {
        return $this->hasMany(Session::class);
    }

    public function parent()
    {
        return $this->belongsTo(self::class, 'parent_id');
    }

    public function openSprint()
    {
        return $this->getProject()->sprints()->open()->orderBy('id', 'desc')->first() ?? new Sprint();
    }

    public function getClient()
    {
        return $this->getProject()->getClient();
    }

    public function getProject()
    {
        return $this->getRelation(Project::class);
    }

    public function getParent()
    {
        return $this->parent ?? new self();
    }

    public function getShortNameAttribute()
    {
        return substr($this->name, 0, $shortNameLength = 50).(strlen($this->name) > $shortNameLength ? '...' : '');
    }
}
