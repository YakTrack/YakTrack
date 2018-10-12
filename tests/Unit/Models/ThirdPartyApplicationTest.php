<?php

namespace Tests\Unit\Models;

use App\Models\Session;
use App\Models\Task;
use App\Models\ThirdPartyApplication;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ThirdPartyApplicationTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function the_total_linked_session_duration_for_task_for_humans_method_returns_expected_results()
    {
        $thirdPartyApplication = factory(ThirdPartyApplication::class)->states(['wrike'])->create();

        $task = factory(Task::class)->create();

        factory(Session::class, 2)->create([
            'task_id'    => $task->id,
            'started_at' => '2018-01-01 00:00:00',
            'ended_at'   => '2018-01-01 00:01:00',
        ])->each(function ($session) use ($thirdPartyApplication) {
            $session->linkTo($thirdPartyApplication);
        });

        $this->assertEquals('0:02:00', $thirdPartyApplication->totalLinkedSessionDurationForTaskForHumans($task));
    }
}
