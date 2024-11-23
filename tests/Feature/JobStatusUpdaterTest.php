<?php

namespace Brainstud\LaravelJobTracker\Tests\Feature;

use Brainstud\LaravelJobTracker\JobStatusUpdater;
use Brainstud\LaravelJobTracker\JobStatusValue;
use Brainstud\LaravelJobTracker\Tests\Data\Label;
use Brainstud\LaravelJobTracker\Tests\Data\NoTrackTestJob;
use Brainstud\LaravelJobTracker\Tests\TestCase;
use Illuminate\Contracts\Bus\Dispatcher;
use Illuminate\Support\Facades\Artisan;

class JobStatusUpdaterTest extends TestCase
{
    public function test_update_trackable_job()
    {
        /** @var JobStatusUpdater $updater */
        $updater = app(JobStatusUpdater::class);

        [$job, $label] = $this->createBaseJob();

        $this->assertDatabaseHas('job_statuses', [
            'subject_type' => $label->getMorphClass(),
            'subject_id' => $label->id,
            'status' => JobStatusValue::PENDING,
        ]);

        $updater->update($job, [
            'status' => JobStatusValue::SUCCESS,
        ]);

        $this->assertDatabaseHas('job_statuses', [
            'subject_type' => $label->getMorphClass(),
            'subject_id' => $label->id,
            'status' => JobStatusValue::SUCCESS,
        ]);
    }

    public function test_update_event()
    {
        [$job, $label] = $this->createBaseJob();

        $this->assertDatabaseHas('job_statuses', [
            'subject_type' => $label->getMorphClass(),
            'subject_id' => $label->id,
            'status' => JobStatusValue::PENDING,
        ]);

        app(Dispatcher::class)->dispatch($job);

        Artisan::call('queue:work', [
            '--once' => 1,
        ]);

        $this->assertDatabaseHas('job_statuses', [
            'subject_type' => $label->getMorphClass(),
            'subject_id' => $label->id,
            'status' => JobStatusValue::SUCCESS,
        ]);
    }

    public function test_update__failed_event()
    {
        [$job, $label] = $this->createBaseJob();
        $job = $job->withException();

        $this->assertDatabaseHas('job_statuses', [
            'subject_type' => $label->getMorphClass(),
            'subject_id' => $label->id,
            'status' => JobStatusValue::PENDING,
        ]);

        app(Dispatcher::class)->dispatch($job);

        Artisan::call('queue:work', [
            '--once' => 1,
        ]);

        $this->assertDatabaseHas('job_statuses', [
            'subject_type' => $label->getMorphClass(),
            'subject_id' => $label->id,
            'status' => JobStatusValue::FAILED,
        ]);
    }

    public function test_update_non_trackable_job()
    {
        $label = Label::factory()->create();
        $job = (new NoTrackTestJob($label))->withFakeQueueInteractions();

        $this->assertDatabaseEmpty('job_statuses');

        app(Dispatcher::class)->dispatch($job);

        Artisan::call('queue:work', [
            '--once' => 1,
        ]);

        $this->assertDatabaseEmpty('job_statuses');
    }
}
