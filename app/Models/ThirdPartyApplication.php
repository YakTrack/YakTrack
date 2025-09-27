<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ThirdPartyApplication extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function totalLinkedSessionDurationForTaskForHumans(Task $task): string
    {
        return $this->linkedSessionsForTask($task)->totalDurationForHumans();
    }

    /**
     * @return \Illuminate\Database\Eloquent\Collection<int, \App\Models\Session>
     */
    public function linkedSessionsForTask(Task $task): \Illuminate\Database\Eloquent\Collection
    {
        return $task->sessions()->linkedTo($this)->get();
    }
}
