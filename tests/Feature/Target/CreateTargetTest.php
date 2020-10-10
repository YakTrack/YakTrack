<?php

namespace Tests\Feature\Target;

use App\Models\Target;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CreateTargetTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function a_user_can_view_the_create_target_page()
    {
        $this->actingAsUser();

        $response = $this->get(route('target.create'));

        $response->assertSuccessful();
    }

    /** @test */
    public function a_user_can_create_a_daily_target()
    {
        $this->withoutExceptionHandling();

        $this->actingAsUser();

        $response = $this->post(route('target.store', $targetDetails = [
            'duration'      => 1,
            'duration_unit' => Target::DURATION_UNITS['DAYS']['key'],
            'value'         => 8,
            'value_unit'    => Target::VALUE_UNITS['HOURS']['key'],
            'billable_only' => 1,
            'starts_at'     => '2020-01-01 00:00:00',
        ]));

        $response->assertRedirect(route('target.index'));

        $this->assertDatabaseHas('targets', $targetDetails);
    }

    /** @test */
    public function a_user_cannot_create_a_daily_target_for_a_date_which_already_has_one()
    {
        $this->actingAsUser();

        $existingTarget = Target::create($targetDetails = [
            'duration'      => 1,
            'duration_unit' => Target::DURATION_UNITS['DAYS']['key'],
            'value'         => 8,
            'value_unit'    => Target::VALUE_UNITS['HOURS']['key'],
            'billable_only' => 1,
            'starts_at'     => '2020-01-01 00:00:00',
        ]);

        $response = $this->post(route('target.store', $targetDetails));

        $response->assertRedirect();
        $response->assertSessionHasErrors();

        $this->assertEquals(1, Target::count());
    }
}
