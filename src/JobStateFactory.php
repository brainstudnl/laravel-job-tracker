<?php

namespace Brainstud\LaravelJobTracker;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<JobState>
 */
class JobStateFactory extends Factory
{
    protected $model = JobState::class;

    public function definition(): array
    {
        return [
            'status' => JobStateValue::PENDING,
        ];
    }
}
