<?php

namespace App\Http\Controllers\Task;

use App\Http\Controllers\Controller;
use App\Models\Session;
use App\Models\Task;
use Illuminate\Http\Request;

class SessionController extends Controller
{
    public function store(Task $task)
    {
        Session::running()->get()->each(function ($session) {
            $session->stop();
        });

        $session = $task->sessions()->create([
            'started_at' => request('started_at'),
            'ended_at'   => request('ended_at'),
            'sprint_id'  => $task->openSprint()->id,
        ]);

        return redirect()->route('session.index');
    }
}
