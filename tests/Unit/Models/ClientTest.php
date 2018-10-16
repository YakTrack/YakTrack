<?php

namespace Tests\Unit\Models;

use App\Models\Client;
use App\Models\Project;
use App\Models\Session;
use App\Models\Task;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ClientTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function the_sessions_this_week_attribute_returns_all_the_clients_sessions_for_the_week()
    {
        Carbon::setTestNow('2018-01-02 12:00:00');

        $client = factory(Client::class)->create();
        $project = factory(Project::class)->create(['client_id' => $client->id]);
        $task = factory(Task::class)->create(['project_id' => $project->id]);
        $session = factory(Session::class)->create(['task_id' => $task->id]);

        $this->assertTrue($client->sessionsThisWeek->contains(function ($clientSession) use ($session) {
            return $session->id === $clientSession->id;
        }));

        Carbon::setTestNow();
    }
}
