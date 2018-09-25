<?php

namespace Tests;

use Config;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

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
        $user = factory(\App\User::class)->create();

        $this->actingAs($user);

        return $user;
    }

    protected function usingTestDisplayTimeZone($timezone = null)
    {
        Config::set('app.display_timezone', $timezone ?? 'Australia/Sydney');
    }
}
