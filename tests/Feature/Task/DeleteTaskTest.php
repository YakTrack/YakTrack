<?php

namespace Tests\Feature\Task;

use App\Models\Task;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DeleteTaskTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function a_user_can_delete_a_task()
    {
        $task = factory(Task::class)->create(['name' => 'Test Task']);

        $this->actingAsUser();

        $response = $this->delete(route('task.destroy', [
            'task' => $task,
        ]));

        $response->assertRedirect(route('task.index'));

        $this->assertDatabaseMissing('tasks', [
            'id' => $task->id,
        ]);
    }
}
