<?php

namespace Tests\Unit\Models;

use App\Models\Session;
use App\Models\Target;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TargetTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function find_for_date_static_method_returns_expected_target()
    {
        $target = factory(Target::class)->states('for_date')->create([
            'starts_at' => '2020-01-01 00:00:00',
        ]);

        $this->assertTrue(Target::findForDate('2020-01-01')->is($target));
    }

    /** @test */
    public function hours_remaining_method_returns_the_number_of_hours_remaining_for_the_target()
    {
        $target = factory(Target::class)->states('for_date', 'in_hours')->create([
            'starts_at' => '2020-01-01 00:00:00',
            'value' => 8,
        ]);

        $this->assertEquals(8, $target->hoursRemaining());
    }

    /** @test */
    public function hours_remaining_method_returns_the_number_of_hours_remaining_for_the_target_less_any_sessions()
    {
        $target = factory(Target::class)->states('for_date', 'in_hours')->create([
            'starts_at' => '2020-01-01 00:00:00',
            'value' => 7,
        ]);

        $session = factory(Session::class)->create([
            'started_at' => '2020-01-01 00:00:00',
            'ended_at' => '2020-01-01 01:00:00',
        ]);

        $this->assertEquals(7, $target->hoursRemaining());
    }
}
