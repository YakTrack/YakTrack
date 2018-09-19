<?php

namespace Tests\Feature\ExternalTaskManagerSession;

use App\Models\ExternalTaskManager;
use App\Session;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class CreateExternalTaskManagerSessionTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function a_session_can_be_exported_to_an_external_task_manager_with_sessions()
    {
        $externalTaskManager = factory(ExternalTaskManager::class)->states(['wrike'])->create();

        $session = factory(Session::class)->create();

        $session->exportToExternalTaskManager($externalTaskManager);

        $this->assertTrue($session->externalTaskManagerSessions->contains(function ($externalTaskManagerSession) use ($session) {
            return $externalTaskManagerSession->session->id === $session->id;
        }));
    }
}
