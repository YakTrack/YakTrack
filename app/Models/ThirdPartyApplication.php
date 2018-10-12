<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ThirdPartyApplication extends Model
{
    protected $guarded = [];

    public function totalLinkedSessionDurationForTaskForHumans(Task $task)
    {
        return $this->linkedSessionsForTask($task)->totalDurationForHumans();
    }

    public function linkedSessionsForTask(Task $task)
    {
        return $task->sessions()->linkedTo($this)->get();
    }
}
