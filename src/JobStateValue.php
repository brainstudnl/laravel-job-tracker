<?php

namespace Brainstud\LaravelJobTracker;

enum JobStateValue: string
{
    case PENDING = 'pending';
    case PROCESSING = 'processing';
    case SUCCESS = 'success';
    case FAILED = 'failed';
}
