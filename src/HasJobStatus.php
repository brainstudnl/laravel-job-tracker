<?php

namespace Brainstud\LaravelJobTracker;

use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Support\Collection;

/**
 * @property Collection<int, JobStatus> $jobStatuses
 */
trait HasJobStatus
{
    public function jobStatuses(): MorphMany
    {
        return $this->morphMany(JobStatus::class, 'subject');
    }
}
