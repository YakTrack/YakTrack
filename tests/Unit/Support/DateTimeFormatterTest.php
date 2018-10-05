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
    public function the_days_this_week_method_returns_the_expected_results()
    {
        $this->usingTestDisplayTimezone();

        foreach ([
            '2018-01-01 00:00:00 GMT+11',
            '2018-01-04 12:32:00 GMT+11',
            '2018-01-07 00:00:00 GMT+11',
        ] as $testNow) {
            Carbon::setTestNow($testNow);

            $daysThisWeek = (new DateTimeFormatter())->daysThisWeek();
            foreach ([
                '2017-12-31 13:00:00',
                '2018-01-01 13:00:00',
                '2018-01-02 13:00:00',
                '2018-01-03 13:00:00',
                '2018-01-04 13:00:00',
                '2018-01-05 13:00:00',
                '2018-01-06 13:00:00',
            ] as $expectedDate) {
                $this->assertTrue($daysThisWeek->contains(function ($day) use ($expectedDate) {
                    return $day->timezone('UTC')->format('Y-m-d H:i:s') === $expectedDate;
                }), $expectedDate.' not found in days this week for '.$testNow.'. Dates found were: '.$daysThisWeek->map(function ($day) {
                    return $day->format('Y-m-d H:i:s');
                })->implode(', '));
            }
        }

        Carbon::setTestNow();
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
}
