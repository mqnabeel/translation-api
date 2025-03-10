<?php

namespace Tests;

use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    use CreatesApplication;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Disable browser testing
        $this->withoutChrome();
    }

    protected function withoutChrome()
    {
        if (isset($this->app['config'])) {
            $this->app['config']->set('dusk.enabled', false);
            $this->app['config']->set('app.debug', false);
        }
    }

    protected function tearDown(): void
    {
        \Mockery::close();
        parent::tearDown();
    }
}
