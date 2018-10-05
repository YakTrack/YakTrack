<?php

namespace Tests\Feature\Task;

use App\Session;
use App\Task;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ShowTaskTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function a_user_can_view_the_show_task_page_for_a_task()
    {
        $this->withoutExceptionHandling();

        $task = factory(Task::class)->create();

        $session = factory(Session::class)->create([
            'task_id' => $task->id,
        ]);

        $this->actingAsUser();

        $response = $this->get(route('task.show', ['task' => $task]));

        $response->assertSuccessful();

        $response->assertSee($task->name);
        $response->assertViewIs('task.show');
        $response->assertSee($task->description);
        $response->assertSee(route('session.show', ['session' => $session]));
    }
}
