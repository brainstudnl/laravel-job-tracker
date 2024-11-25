<?php

namespace Brainstud\LaravelJobTracker;

use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Support\Collection;

/**
 * @property Collection<int, JobState> $jobStates
 */
trait HasJobStates
{
    public function jobStates(): MorphMany
    {
        return $this->morphMany(JobState::class, 'subject');
    }
}
