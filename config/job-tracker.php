<?php

use Brainstud\LaravelJobTracker\EventManagers\DefaultEventManager;
use Brainstud\LaravelJobTracker\JobState;

return [
    'model' => JobState::class,
    'event_manager' => DefaultEventManager::class,
];
