<?php
/**
 * Created by PhpStorm.
 * User: dominiksecka
 * Date: 2018-12-15
 * Time: 16:28.
 */

namespace Tests\Unit\Support;

use App\Support\DateIntervalFormatter;
use DateInterval;
use Tests\TestCase;

class DateIntervalFormatterTest extends TestCase
{

    /** @test
     * @throws \Exception
     */
    public function convert_date_interval_to_human_readable_format()
    {
        $expected = '6:08:00';

        $dateInterval = new DateInterval('P2Y4DT6H8M');

        $this->assertEquals($expected, (new DateIntervalFormatter())->forHumans($dateInterval));
    }

    /** @test
     * @throws \Exception
     */
    public function get_count_of_hours_from_date_interval()
    {
        $expected = 6;

        $dateInterval = new DateInterval('P2Y4DT6H8M');

        $this->assertEquals($expected, (new DateIntervalFormatter())->numberOfHours($dateInterval));
    }
}