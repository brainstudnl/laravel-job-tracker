<?php

namespace Brainstud\LaravelJobTracker\EventManagers;

use Brainstud\LaravelJobTracker\JobState;
use Brainstud\LaravelJobTracker\JobStateValue;
use Illuminate\Queue\Events\JobExceptionOccurred;
use Illuminate\Queue\Events\JobFailed;
use Illuminate\Queue\Events\JobProcessed;
use Illuminate\Queue\Events\JobProcessing;

class DefaultEventManager extends EventManager
{
    public function before(JobProcessing $event): void
    {
        // Currently not needed since we create the status model in the job's `__construct`
    }

    public function after(JobProcessed $event): void
    {
        if (! $event->job->hasFailed()) {
            $this->getUpdater()->update($event, [
                'status' => JobStateValue::SUCCESS,
            ]);
        }
    }

    public function failing(JobFailed $event): void
    {
        $this->getUpdater()->update($event, [
            'status' => JobStateValue::FAILED,
            'exception' => JobState::convertException($event->exception),
        ]);
    }

    public function exceptionOccurred(JobExceptionOccurred $event): void
    {
        $this->getUpdater()->update($event, [
            'status' => JobStateValue::FAILED,
            'exception' => JobState::convertException($event->exception),
        ]);
    }
}
