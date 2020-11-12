<?php

namespace Tests;

use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Illuminate\Foundation\Testing\TestResponse;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Config;
use PHPUnit\Framework\Assert;

abstract class TestCase extends BaseTestCase
{
    /**
     * The base URL to use while testing the application.
     *
     * @var string
     */
    protected $baseUrl = 'http://localhost';

    /**
     * Creates the application.
     *
     * @return \Illuminate\Foundation\Application
     */
    public function createApplication()
    {
        $app = require __DIR__.'/../bootstrap/app.php';

        $app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

        return $app;
    }

    protected function actingAsUser()
    {
        $user = factory(\App\Models\User::class)->create();

        $this->actingAs($user);

        return $user;
    }

    protected function usingTestDisplayTimeZone($timezone = null)
    {
        Config::set('app.display_timezone', $timezone ?? 'Australia/Sydney');
    }

    protected function assertArrayMatches($expected, $tested, $address = null)
    {
        $this->assertIsArray($tested, "Failed asserting that $address is an array");

        foreach ($expected as $key => $value) {

            $compoundKey = $address ? "$address.$key" : $key;
            $this->assertArrayHasKey($key, $tested, "Failed asserting that array has key $compoundKey");

            if (is_array($value)) {
                $this->assertArrayMatches($value, $tested[$key], $compoundKey);
            } else {
                $this->assertEquals(
                    $value,
                    $tested[$key],
                    "Failed asserting that array has key $compoundKey equal to $value. Actual value was {$tested[$key]}"
                );
            }
        }
    }

    protected function setUp(): void
    {
        parent::setUp();

        TestResponse::macro('props', function ($key = null) {
            $props = json_decode(json_encode($this->original->getData()['page']['props']), JSON_OBJECT_AS_ARRAY);

            if ($key) {
                return Arr::get($props, $key);
            }

            return $props;
        });

        TestResponse::macro('assertHasProp', function ($key) {
            Assert::assertTrue(Arr::has($this->props(), $key), "Failed asserting that prop '$key' exists");

            return $this;
        });

        TestResponse::macro('assertPropValue', function ($key, $value) {
            $this->assertHasProp($key);

            if (is_callable($value)) {
                $value($this->props($key));
            } else {
                Assert::assertEquals($this->props($key), $value, "Failed asserting that $key prop is equal to $value. Got {$this->props($key)}");
            }

            return $this;
        });

        TestResponse::macro('assertPropValues', function ($values) {
            foreach ($values as $key => $value) {
                $this->assertPropValue($key, $value);
            }
        });

        TestResponse::macro('assertPropCount', function ($key, $count) {
            $this->assertHasProp($key);

            Assert::assertCount($count, $this->props($key));

            return $this;
        });
    }
}
