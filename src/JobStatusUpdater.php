<?php

namespace Brainstud\LaravelJobTracker;

use Illuminate\Queue\Events\JobExceptionOccurred;
use Illuminate\Queue\Events\JobFailed;
use Illuminate\Queue\Events\JobProcessed;
use Illuminate\Queue\Events\JobProcessing;
use Illuminate\Support\Facades\Log;

class JobStatusUpdater
{
    public function update($job, array $data)
    {
        if ($this->isEvent($job)) {
            $this->updateEvent($job, $data);
        } else {
            $this->updateJob($job, $data);
        }
    }

    protected function updateEvent(
        JobProcessing|JobProcessed|JobFailed|JobExceptionOccurred $event, array $data
    ) {
        $job = $this->parseJob($event);
        $jobStatus = $this->getJobStatus($job);

        if (! $jobStatus) {
            return;
        }

        $jobStatus->update($data);
    }

    protected function updateJob($job, array $data)
    {
        $job->jobStatus->update($data);
    }

    /**
     * Retrieve the job from an Event.
     *
     * @return mixed|null
     */
    protected function parseJob(JobProcessing|JobProcessed|JobFailed|JobExceptionOccurred $event)
    {
        try {
            $payload = $event->job->payload();

            return unserialize($payload['data']['command']);
        } catch (\Throwable $e) {
            Log::error($e->getMessage());

            return null;
        }
    }

    protected function getJobStatusId($job)
    {
        try {
            if ($job instanceof TrackableJob || method_exists($job, 'getJobStatusId')) {
                return $job->getJobStatusId();
            }
        } catch (\Throwable $e) {
            Log::error($e->getMessage());

            return null;
        }

        return null;
    }

    /**
     * Retrieve the `JobStatus` from a Job
     */
    protected function getJobStatus($job): ?JobStatus
    {
        if (isset($job->jobStatus) && $job->jobStatus instanceof JobStatus) {
            return $job->jobStatus;
        }

        return null;
    }

    /**
     * Check if the Job is an Event.
     *
     * Check if it's a JobProcessing|JobProcessed|JobFailed|JobExceptionOccurred Laravel event.
     */
    protected function isEvent($job): bool
    {
        return $job instanceof JobProcessing
            || $job instanceof JobProcessed
            || $job instanceof JobFailed
            || $job instanceof JobExceptionOccurred;
    }
}
