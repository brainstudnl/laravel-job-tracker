<?php

namespace Brainstud\LaravelJobTracker\Tests\Data;

use Brainstud\LaravelJobTracker\Trackable;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;

class NoTrackTestJob implements ShouldQueue
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
        //
    }
}
