<?php

namespace Tests\Feature\ExternalTaskManager;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class CreateExternalTaskManagerTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function a_user_can_create_an_external_task_manager()
    {
        $this->withoutExceptionHandling();

        $this->actingAsUser();

        $response = $this->post(route('external-task-manager.store'), [
            'type' => 'wrike',
            'name' => 'Test Wrike Account',
        ]);

        $response->assertRedirect(route('external-task-manager.index'));

        $this->assertDatabaseHas('external_task_managers', [
            'type' => 'wrike',
            'name' => 'Test Wrike Account'
        ]);
    }
}
