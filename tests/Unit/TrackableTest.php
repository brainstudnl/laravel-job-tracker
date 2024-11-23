<?php

namespace Brainstud\LaravelJobTracker\Tests\Unit;

use Brainstud\LaravelJobTracker\JobStatusValue;
use Brainstud\LaravelJobTracker\Tests\Data\Label;
use Brainstud\LaravelJobTracker\Tests\Data\NoTrackTestJob;
use Brainstud\LaravelJobTracker\Tests\TestCase;

class TrackableTest extends TestCase
{
    public function test_job_creation_sets_status_pending(): void
    {
        [, $label] = $this->createBaseJob();

        $this->assertDatabaseHas('job_statuses', [
            'subject_type' => $label->getMorphClass(),
            'subject_id' => $label->id,
            'status' => JobStatusValue::PENDING,
        ]);
    }

    public function test_job_handle_sets_status_success_by_default(): void
    {
        [$job, $label] = $this->createBaseJob();

        $job->withManualTracking()->handle();

        $this->assertDatabaseHas('job_statuses', [
            'subject_type' => $label->getMorphClass(),
            'subject_id' => $label->id,
            'status' => JobStatusValue::SUCCESS,
        ]);
    }

    public function test_job_handle_with_exception(): void
    {
        [$job, $label] = $this->createBaseJob();

        $this->callWithException(
            fn () => $job
                ->withException()
                ->withManualTracking()
                ->handle(),
            \Exception::class
        );

        $this->assertDatabaseHas('job_statuses', [
            'subject_type' => $label->getMorphClass(),
            'subject_id' => $label->id,
            'status' => JobStatusValue::FAILED,
        ]);
    }

    public function test_job_without_trackable_job_interface_does_not_track(): void
    {
        $label = Label::factory()->create();
        $job = (new NoTrackTestJob($label))->withFakeQueueInteractions();

        $job->handle();

        $this->assertDatabaseEmpty('job_statuses');
    }
}
