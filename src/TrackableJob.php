<?php

namespace Brainstud\LaravelJobTracker;

interface TrackableJob
{
    public function getJobStateId();
}
