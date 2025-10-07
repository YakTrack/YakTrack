<?php

use App\Models\Client;
use App\Models\Project;
use App\Models\Session;
use App\Models\Task;
use Carbon\Carbon;

it('returns all the clients sessions for the week', function () {
    Carbon::setTestNow('2018-01-02 12:00:00');

    $client = Client::factory()->create();
    $project = Project::factory()->create(['client_id' => $client->id]);
    $task = Task::factory()->create(['project_id' => $project->id]);
    $session = Session::factory()->create([
        'started_at' => Carbon::now(),
        'ended_at'   => Carbon::now(),
        'task_id'    => $task->id,
    ]);

    expect($client->sessionsThisWeek->contains(function ($clientSession) use ($session) {
        return $session->id === $clientSession->id;
    }))->toBeTrue();

    Carbon::setTestNow();
});
