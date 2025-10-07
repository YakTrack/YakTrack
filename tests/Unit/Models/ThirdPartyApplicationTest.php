<?php

use App\Models\Session;
use App\Models\Task;
use App\Models\ThirdPartyApplication;

it('returns expected results for total_linked_session_duration_for_task_for_humans method', function () {
    $thirdPartyApplication = ThirdPartyApplication::factory()->wrike()->create();

    $task = Task::factory()->create();

    Session::factory()->count(2)->create([
        'task_id'    => $task->id,
        'started_at' => '2018-01-01 00:00:00',
        'ended_at'   => '2018-01-01 00:01:00',
    ])->each(function ($session) use ($thirdPartyApplication) {
        $session->linkTo($thirdPartyApplication);
    });

    expect($thirdPartyApplication->totalLinkedSessionDurationForTaskForHumans($task))->toBe('0:02:00');
});