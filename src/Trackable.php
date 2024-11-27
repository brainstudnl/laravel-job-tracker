<?php

namespace Brainstud\LaravelJobTracker;

use Illuminate\Database\Eloquent\Model;

trait Trackable
{
    protected int $statusId;

    public JobState $jobState;

    /**
     * Start tracking the Job.
     *
     * Creates a `JobState` model in the database and links the given subject.
     */
    protected function startTracking(Model $subject): void
    {
        if (! $this instanceof TrackableJob) {
            return;
        }
        // Create the model.
        $this->jobState = new JobState([
            'status' => JobStateValue::PENDING,
        ]);
        $this->jobState->subject()->associate($subject);
        $this->jobState->save();
    }

    /**
     * End tracking the job.
     *
     * This method ends the job tracking. It updates the model
     * in the database with the appropriate status.
     *
     * @param  \Brainstud\LaravelJobTracker\JobStateValue  $status  The status that should be saved.
     */
    protected function endTracking(?JobStateValue $status = JobStateValue::SUCCESS): void
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
        $this->updateStatus(JobStateValue::FAILED);
    }

    /**
     * Fail the job with a given exception
     *
     * Makes sure the job state will be JobStateValue::FAILED and the given exception
     * is added to the database row.
     */
    protected function failWith(\Throwable $exception): void
    {
        $this->jobState->update(['exception' => JobState::convertException($exception)]);
        $this->failTracking();
    }

    /**
     * Update the job status.
     *
     * @param  \Brainstud\LaravelJobTracker\JobStateValue  $status  The status that should be saved.
     */
    protected function updateStatus(JobStateValue $status): void
    {
        $this->jobState->update(['status' => $status]);
    }

    public function getJobStateId()
    {
        return $this->statusId;
    }
}
