<?php

namespace Brainstud\LaravelJobTracker;

use Brainstud\LaravelJobTracker\EventManagers\EventManager;
use Illuminate\Queue\Events\JobExceptionOccurred;
use Illuminate\Queue\Events\JobFailed;
use Illuminate\Queue\Events\JobProcessed;
use Illuminate\Queue\Events\JobProcessing;
use Illuminate\Queue\QueueManager;
use Illuminate\Support\ServiceProvider;

class LaravelJobTrackerServiceProvider extends ServiceProvider
{
    public function boot()
    {
        $this->loadMigrationsFrom(__DIR__.'/../database/migrations');

        $this->mergeConfigFrom(__DIR__.'/../config/job-tracker.php', 'job-tracker');

        $this->publishes([
            __DIR__.'/../database/migrations/' => database_path('migrations'),
        ], 'migrations');

        $this->publishes([
            __DIR__.'/../config/' => config_path(),
        ], 'config');

        $this->bootListeners();
    }

    private function bootListeners()
    {
        /** @var EventManager $eventManager */
        $eventManager = app(config('job-tracker.event_manager'));

        // Add Event listeners
        app(QueueManager::class)->before(function (JobProcessing $event) use ($eventManager) {
            $eventManager->before($event);
        });
        app(QueueManager::class)->after(function (JobProcessed $event) use ($eventManager) {
            $eventManager->after($event);
        });
        app(QueueManager::class)->failing(function (JobFailed $event) use ($eventManager) {
            $eventManager->failing($event);
        });
        app(QueueManager::class)->exceptionOccurred(function (JobExceptionOccurred $event) use ($eventManager) {
            $eventManager->exceptionOccurred($event);
        });
    }
}
