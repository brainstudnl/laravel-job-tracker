<?php

namespace Brainstud\LaravelJobTracker\Tests\Unit;

use Brainstud\LaravelJobTracker\JobStatusValue;
use Brainstud\LaravelJobTracker\Tests\Data\Label;
use Brainstud\LaravelJobTracker\Tests\Data\TestJob;
use Brainstud\LaravelJobTracker\Tests\TestCase;
use Illuminate\Support\Facades\Artisan;

class EventManagerTest extends TestCase
{
    public function test_default_event_manager_catches_failed_exceptions()
    {
        $label = Label::factory()->create();
        $job = (new TestJob($label))->withException();

        $this->callWithException(function () use ($job) {
            dispatch($job);

            Artisan::call('queue:work', [
                '--once' => 1,
            ]);
        }, \Exception::class);

        $this->assertDatabaseHas('job_statuses', [
            'subject_type' => $label->getMorphClass(),
            'subject_id' => $label->id,
            'status' => JobStatusValue::FAILED,
        ]);
    }
}
