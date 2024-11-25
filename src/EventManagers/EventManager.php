<?php

namespace Brainstud\LaravelJobTracker\EventManagers;

use Brainstud\LaravelJobTracker\JobStateUpdater;
use Illuminate\Queue\Events\JobExceptionOccurred;
use Illuminate\Queue\Events\JobFailed;
use Illuminate\Queue\Events\JobProcessed;
use Illuminate\Queue\Events\JobProcessing;

abstract class EventManager
{
    abstract public function before(JobProcessing $event): void;

    abstract public function after(JobProcessed $event): void;

    abstract public function failing(JobFailed $event): void;

    abstract public function exceptionOccurred(JobExceptionOccurred $event): void;

    private JobStateUpdater $updater;

    public function __construct(JobStateUpdater $updater)
    {
        $this->updater = $updater;
    }

    /**
     * @return JobStateUpdater
     */
    protected function getUpdater()
    {
        return $this->updater;
    }
}
