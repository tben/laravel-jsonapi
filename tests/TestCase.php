<?php

namespace Tben\LaravelJsonAPI\Tests;

use Orchestra\Testbench\TestCase as TestCaseBase;

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
            'Tben\LaravelJsonAPI\Providers\LaravelServiceProvider',
        ];
    }

    protected function getEnvironmentSetUp($app)
    {
        // perform environment setup
    }
}