<?php

namespace Brainstud\LaravelJobTracker\Tests\Feature;

use Brainstud\LaravelJobTracker\JobStateValue;
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

        dispatch($job);

        Artisan::call('queue:work', [
            '--once' => 1,
        ]);

        $this->assertDatabaseHas('job_states', [
            'subject_type' => $label->getMorphClass(),
            'subject_id' => $label->id,
            'status' => JobStateValue::FAILED,
        ]);
    }
}
