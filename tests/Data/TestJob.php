<?php

namespace Brainstud\LaravelJobTracker\Tests\Data;

use Brainstud\LaravelJobTracker\Trackable;
use Brainstud\LaravelJobTracker\TrackableJob;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;

class TestJob implements ShouldQueue, TrackableJob
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use Trackable;

    private bool $withException = false;

    private bool $withManualTracking = false;

    public function __construct(public Label $label)
    {
        $this->startTracking($label);
    }

    public function handle()
    {
        if ($this->withException) {
            if ($this->withManualTracking) {
                $this->failTracking();
            }

            throw new \Exception('This is a test exception.');
        }

        if ($this->withManualTracking) {
            $this->endTracking();
        }
    }

    public function withException(): self
    {
        $this->withException = true;

        return $this;
    }

    public function withManualTracking(): self
    {
        $this->withManualTracking = true;

        return $this;
    }
}
