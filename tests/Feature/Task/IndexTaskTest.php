<?php

namespace Tests\Feature\Task;

use App\Models\Task;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class IndexTaskTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function a_user_can_see_a_list_of_tasks()
    {
        $parentTask = factory(Task::class)->create();
        $task = factory(Task::class)->create(['parent_id' => $parentTask->id]);

        $this->actingAsUser();

        $response = $this->get(route('task.index'));

        $response->assertSuccessful();

        $response->assertSee($task->name);
        $response->assertSee($task->parent->shortName);
        $response->assertSee($task->project->name);
    }
}
