<?php

namespace Brainstud\LaravelJobTracker\Tests\Feature;

use Brainstud\LaravelJobTracker\JobStateValue;
use Brainstud\LaravelJobTracker\Tests\Data\Label;
use Brainstud\LaravelJobTracker\Tests\Data\NoTrackTestJob;
use Brainstud\LaravelJobTracker\Tests\TestCase;
use Illuminate\Support\Facades\Artisan;

class TrackableTest extends TestCase
{
    public function test_job_creation_sets_status_pending(): void
    {
        [, $label] = $this->createBaseJob();

        $this->assertDatabaseHas('job_states', [
            'subject_type' => $label->getMorphClass(),
            'subject_id' => $label->id,
            'status' => JobStateValue::PENDING,
        ]);
    }

    public function test_job_handle_sets_status_success_by_default(): void
    {
        [$job, $label] = $this->createBaseJob();

        $job->withManualTracking()->handle();

        $this->assertDatabaseHas('job_states', [
            'subject_type' => $label->getMorphClass(),
            'subject_id' => $label->id,
            'status' => JobStateValue::SUCCESS,
        ]);
    }

    public function test_job_handle_with_exception_manual(): void
    {
        [$job, $label] = $this->createBaseJob();

        try {
            $job->withException()
                ->withManualTracking()
                ->handle();
        } catch (\Exception $exception) {
            $this->assertDatabaseHas('job_states', [
                'subject_type' => $label->getMorphClass(),
                'subject_id' => $label->id,
                'status' => JobStateValue::FAILED,
            ]);
        }
    }

    public function test_job_handle_with_exception_events(): void
    {
        [$job, $label] = $this->createBaseJob();

        dispatch($job->withException());

        Artisan::call('queue:work', [
            '--once' => 1,
        ]);

        $this->assertDatabaseHas('job_states', [
            'subject_type' => $label->getMorphClass(),
            'subject_id' => $label->id,
            'status' => JobStateValue::FAILED,
        ]);
    }

    public function test_job_without_trackable_job_interface_does_not_track(): void
    {
        $label = Label::factory()->create();
        $job = (new NoTrackTestJob($label))->withFakeQueueInteractions();

        $job->handle();

        $this->assertDatabaseEmpty('job_states');
    }
}
