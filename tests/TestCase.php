<?php

namespace Tben\LaravelJsonAPI\Tests;

use Orchestra\Testbench\TestCase as TestCaseBase;
use Tben\LaravelJsonAPI\Providers\LaravelServiceProvider;

class TestCase extends TestCaseBase
{
    public function setUp(): void
    {
        parent::setUp();
        // additional setup
    }

    protected function getPackageProviders($app)
    {
        return [
            LaravelServiceProvider::class
        ];
    }

    protected function getEnvironmentSetUp($app)
    {
        // perform environment setup
    }
}