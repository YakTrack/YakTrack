<?php

namespace App\Http\Controllers\Task;

use App\Http\Controllers\Controller;
use App\Models\Session;
use App\Models\Task;
use App\Support\DateTimeFormatter;
use Illuminate\Http\Request;

class SessionController extends Controller
{
    protected $dateTimeFormatter;

    public function __construct(DateTimeFormatter $dateTimeFormatter)
    {
        $this->dateTimeFormatter = $dateTimeFormatter;
    }

    public function store(Task $task)
    {
        Session::running()->get()->each(function ($session) {
            $session->stop();
        });

        $session = $task->sessions()->create([
            'started_at' => $this->dateTimeFormatter->utcFormat(request('started_at')),
            'ended_at'   => request('ended_at') ? $this->dateTimeFormatter->utcFormat(request('ended_at')) : null,
            'sprint_id'  => $task->openSprint()->id,
        ]);

        return redirect()->route('session.index');
    }
}
