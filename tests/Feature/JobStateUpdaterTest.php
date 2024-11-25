<?php

namespace Brainstud\LaravelJobTracker\Tests\Feature;

use Brainstud\LaravelJobTracker\JobStateUpdater;
use Brainstud\LaravelJobTracker\JobStateValue;
use Brainstud\LaravelJobTracker\Tests\Data\Label;
use Brainstud\LaravelJobTracker\Tests\Data\NoTrackTestJob;
use Brainstud\LaravelJobTracker\Tests\TestCase;
use Illuminate\Contracts\Bus\Dispatcher;
use Illuminate\Support\Facades\Artisan;

class JobStateUpdaterTest extends TestCase
{
    public function test_update_trackable_job()
    {
        /** @var JobStateUpdater $updater */
        $updater = app(JobStateUpdater::class);

        [$job, $label] = $this->createBaseJob();

        $this->assertDatabaseHas('job_states', [
            'subject_type' => $label->getMorphClass(),
            'subject_id' => $label->id,
            'status' => JobStateValue::PENDING,
        ]);

        $updater->update($job, [
            'status' => JobStateValue::SUCCESS,
        ]);

        $this->assertDatabaseHas('job_states', [
            'subject_type' => $label->getMorphClass(),
            'subject_id' => $label->id,
            'status' => JobStateValue::SUCCESS,
        ]);
    }

    public function test_update_event()
    {
        [$job, $label] = $this->createBaseJob();

        $this->assertDatabaseHas('job_states', [
            'subject_type' => $label->getMorphClass(),
            'subject_id' => $label->id,
            'status' => JobStateValue::PENDING,
        ]);

        app(Dispatcher::class)->dispatch($job);

        Artisan::call('queue:work', [
            '--once' => 1,
        ]);

        $this->assertDatabaseHas('job_states', [
            'subject_type' => $label->getMorphClass(),
            'subject_id' => $label->id,
            'status' => JobStateValue::SUCCESS,
        ]);
    }

    public function test_update__failed_event()
    {
        [$job, $label] = $this->createBaseJob();
        $job = $job->withException();

        $this->assertDatabaseHas('job_states', [
            'subject_type' => $label->getMorphClass(),
            'subject_id' => $label->id,
            'status' => JobStateValue::PENDING,
        ]);

        app(Dispatcher::class)->dispatch($job);

        Artisan::call('queue:work', [
            '--once' => 1,
        ]);

        $this->assertDatabaseHas('job_states', [
            'subject_type' => $label->getMorphClass(),
            'subject_id' => $label->id,
            'status' => JobStateValue::FAILED,
        ]);
    }

    public function test_update_non_trackable_job()
    {
        $label = Label::factory()->create();
        $job = (new NoTrackTestJob($label))->withFakeQueueInteractions();

        $this->assertDatabaseEmpty('job_states');

        app(Dispatcher::class)->dispatch($job);

        Artisan::call('queue:work', [
            '--once' => 1,
        ]);

        $this->assertDatabaseEmpty('job_states');
    }
}
