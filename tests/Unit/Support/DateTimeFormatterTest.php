<?php

namespace Tests\Unit\Support;

use App\Support\DateTimeFormatter;
use Carbon\Carbon;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class DateTimeFormatterTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function the_start_of_week_method_returns_the_utc_date_time_of_the_start_of_the_week_according_to_display_timezone()
    {
        $this->usingTestDisplayTimezone();

        Carbon::setTestNow('2018-01-01 00:00:00');

        $this->assertEquals('2017-12-31 13:00:00', (new DateTimeFormatter)->startOfWeek());
    }

    /** @test */
    public function the_end_of_week_method_returns_the_utc_date_time_of_the_end_of_the_week_according_to_display_timezone()
    {
        $this->usingTestDisplayTimezone();

        Carbon::setTestNow('2018-01-01 00:00:00');

        $this->assertEquals('2018-01-07 13:00:00', (new DateTimeFormatter)->endOfWeek());
    }
}
