<?php

namespace Tests\Unit\Support;

use App\Support\DateTimeFormatter;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DateTimeFormatterTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function the_start_of_week_method_returns_the_utc_date_time_of_the_start_of_the_week_according_to_display_timezone()
    {
        $this->usingTestDisplayTimezone();

        Carbon::setTestNow('2018-01-01 00:00:00');

        $this->assertEquals('2017-12-31 13:00:00', (new DateTimeFormatter())->startOfWeek());
    }

    /** @test */
    public function the_end_of_week_method_returns_the_utc_date_time_of_the_end_of_the_week_according_to_display_timezone()
    {
        $this->usingTestDisplayTimezone();

        Carbon::setTestNow('2018-01-01 00:00:00');

        $this->assertEquals('2018-01-07 13:00:00', (new DateTimeFormatter())->endOfWeek());
    }

    /** @test */
    public function to_utc_method_returns_expected_value()
    {
        $this->usingTestDisplayTimezone();

        $formatter = app(DateTimeFormatter::class);

        $this->assertEquals('2018-01-01 00:00:00', $formatter->toUTC(Carbon::parse('2018-01-01 00:00:00')));
        $this->assertEquals('2018-01-01 00:00:00', $formatter->toUTC(Carbon::parse('2018-01-01 00:00:00')->timezone('Australia/Sydney')));
    }

    /** @test */
    public function utc_format_method_returns_expected_value()
    {
        $this->usingTestDisplayTimezone();

        $formatter = app(DateTimeFormatter::class);

        $this->assertEquals('2017-12-31 23:00:00', $formatter->utcFormat(Carbon::parse('2018-01-01T09:00:00+10:00')));
        $this->assertEquals('2018-01-01 00:00:00', $formatter->utcFormat(Carbon::parse('2018-01-01 00:00:00')->timezone('Australia/Sydney')));
    }

    /** @test */
    public function the_days_this_week_method_returns_the_expected_results()
    {
        $this->usingTestDisplayTimezone();

        Carbon::setTestNow(Carbon::parse('2018-10-16 21:12:14.757632 UTC'));

        $daysThisWeek = (new DateTimeFormatter())->daysThisWeek();

        $this->assertEquals(collect([
            0 => Carbon::__set_state([
                'date'          => '2018-10-15 00:00:00.000000',
                'timezone_type' => 3,
                'timezone'      => 'Australia/Sydney',
            ]),
            1 => Carbon::__set_state([
                'date'          => '2018-10-16 00:00:00.000000',
                'timezone_type' => 3,
                'timezone'      => 'Australia/Sydney',
            ]),
            2 => Carbon::__set_state([
                'date'          => '2018-10-17 00:00:00.000000',
                'timezone_type' => 3,
                'timezone'      => 'Australia/Sydney',
            ]),
            3 => Carbon::__set_state([
                'date'          => '2018-10-18 00:00:00.000000',
                'timezone_type' => 3,
                'timezone'      => 'Australia/Sydney',
            ]),
            4 => Carbon::__set_state([
                'date'          => '2018-10-19 00:00:00.000000',
                'timezone_type' => 3,
                'timezone'      => 'Australia/Sydney',
            ]),
            5 => Carbon::__set_state([
                'date'          => '2018-10-20 00:00:00.000000',
                'timezone_type' => 3,
                'timezone'      => 'Australia/Sydney',
            ]),
            6 => Carbon::__set_state([
                'date'          => '2018-10-21 00:00:00.000000',
                'timezone_type' => 3,
                'timezone'      => 'Australia/Sydney',
            ]),
        ]), $daysThisWeek);

        $this->assertTrue($daysThisWeek[2]->isToday());

        Carbon::setTestNow();
    }
}
