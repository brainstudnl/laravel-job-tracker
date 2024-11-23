<?php

namespace Brainstud\LaravelJobTracker;

use Illuminate\Database\Eloquent\Model;

trait Trackable
{
    /** @var int */
    protected $statusId;

    public JobStatus $jobStatus;

    /**
     * Start tracking the Job.
     *
     * Creates a `JobStatus` model in the database and links the given subject.
     */
    protected function startTracking(Model $subject): void
    {
        if (! $this instanceof TrackableJob) {
            return;
        }
        // Create the model.
        $this->jobStatus = new JobStatus([
            'status' => JobStatusValue::PENDING,
        ]);
        $this->jobStatus->subject()->associate($subject);
        $this->jobStatus->save();
    }

    /**
     * End tracking the job.
     *
     * This method ends the job tracking. It updates the model
     * in the database with the appropriate status.
     *
     * @param  \Brainstud\LaravelJobTracker\JobStatusValue  $status  The status that should be saved.
     */
    protected function endTracking(?JobStatusValue $status = JobStatusValue::SUCCESS): void
    {
        $this->updateStatus($status);
    }

    /**
     * Fail the job.
     *
     * This method ends the job tracking. It updates the model
     * in the database with the a failed.
     */
    protected function failTracking(): void
    {
        $this->updateStatus(JobStatusValue::FAILED);
    }

    /**
     * Update the job status.
     *
     * @param  \Brainstud\LaravelJobTracker\JobStatusValue  $status  The status that should be saved.
     */
    protected function updateStatus(JobStatusValue $status): void
    {
        $this->jobStatus->update(['status' => $status]);
    }

    public function getJobStatusId()
    {
        return $this->statusId;
    }
}
