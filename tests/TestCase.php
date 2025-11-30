<?php

namespace SanSanLabs\Userstamps\Tests;

use Orchestra\Testbench\TestCase as Orchestra;
use SanSanLabs\Userstamps\UserstampsServiceProvider;

class TestCase extends Orchestra
{
    protected function setUp(): void
    {
        parent::setUp();
    }

    protected function getPackageProviders($app): array
    {
        return [UserstampsServiceProvider::class];
    }
}
