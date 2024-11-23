<?php

namespace Brainstud\LaravelJobTracker;

enum JobStatusValue: string
{
    case PENDING = 'pending';
    case PROCESSING = 'processing';
    case SUCCESS = 'success';
    case FAILED = 'failed';
}
