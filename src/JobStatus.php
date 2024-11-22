<?php

namespace Brainstud\LaravelJobTracker;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;

/**
 * Brainstud\LaravelJobTracker.
 *
 * @property int $id
 * @property string $job_id
 * @property JobStatusValue $status
 * @property string $queue
 * @property Model $subject
 * @property int $subject_id
 * @property string $job Reference to the specific job to be executed. Allows for querying specific actions for a subject.
 * @property string $exception If an exception has been thrown, it will be saved in serialized form here.
 * @property Carbon $created_at
 * @property Carbon $updated_at
 *
 * @mixin \Eloquent
 */
class JobStatus extends Model
{
    protected $table = 'job_statuses';

    protected $fillable = [
        'status',
        'queue',
        'job',
        'job_id,',
    ];

    protected function casts()
    {
        return [
            'status' => JobStatusValue::class,
        ];
    }

    public function subject(): MorphTo
    {
        return $this->morphTo();
    }
}
