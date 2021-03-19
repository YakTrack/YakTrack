<?php

namespace Tests\Feature\Session;

use App\Models\Invoice;
use App\Models\Session;
use App\Models\SessionCategory;
use App\Models\Sprint;
use App\Models\Task;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class EditSessionsTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function a_user_can_edit_multiple_sessions_with_a_patch_request()
    {
        $this->withoutExceptionHandling();

        $sessions = factory(Session::class, 2)->create([
            'is_billable' => 0,
        ]);

        $this->actingAsUser();

        $response = $this->patch(
            route('sessions.update'), 
            [
                'sessions' => [
                    $sessions[0]->id => [
                        'is_billable' => 1,
                    ],
                    $sessions[1]->id => [
                        'is_billable' => 1,
                    ],
                ]
            ]
        );

        $response->assertSuccessful();

        $this->assertDatabaseHas('sessions', [
            'id' => $sessions[0]->id,
            'is_billable' => 1,
        ]);

        $this->assertDatabaseHas('sessions', [
            'id' => $sessions[1]->id,
            'is_billable' => 1,
        ]);
    }
}
