<?php

namespace Brainstud\LaravelJobTracker\Tests;

use Brainstud\LaravelJobTracker\LaravelJobTrackerServiceProvider;
use Brainstud\LaravelJobTracker\Tests\Data\Label;
use Brainstud\LaravelJobTracker\Tests\Data\TestJob;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Orchestra\Testbench\TestCase as OrchestraTestCase;

class TestCase extends OrchestraTestCase
{
    use DatabaseMigrations;

    protected function getEnvironmentSetUp($app)
    {
        $app['config']->set('database.default', 'testing');
        $app['config']->set('database.connections.testing', [
            'driver' => 'sqlite',
            'database' => ':memory:',
            'prefix' => '',
        ]);
    }

    protected function setUp(): void
    {
        parent::setUp();

        $appMigrations = realpath(__DIR__.'/database/migrations');
        $testMigrations = realpath(__DIR__.'/../database/migrations');

        $this->loadMigrationsFrom($appMigrations);
        $this->loadMigrationsFrom($testMigrations);
    }

    protected function getPackageProviders($app)
    {
        return [
            LaravelJobTrackerServiceProvider::class,
        ];
    }

    protected function callWithException(callable $fn, string $exceptionClass)
    {
        try {
            $fn();
        } catch (\Exception $e) {
            $this->assertEquals($exceptionClass, $e::class);
        }
    }

    /**
     * Create a base job for a label
     *
     * @return array{TestJob, Label}
     */
    protected function createBaseJob(): array
    {
        $label = Label::factory()->create();
        $job = (new TestJob($label))->withFakeQueueInteractions();

        return [$job, $label];
    }
}
