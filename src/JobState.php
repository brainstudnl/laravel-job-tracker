<?php

namespace Brainstud\LaravelJobTracker;

use Brainstud\HasIdentifier\HasIdentifier;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;

/**
 * Brainstud\LaravelJobTracker.
 *
 * @property int $id
 * @property string $identifier
 * @property string $job_id
 * @property JobStateValue $status
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
class JobState extends Model
{
    /** @use HasFactory<JobStateFactory> */
    use HasFactory;

    use HasIdentifier;

    protected $fillable = [
        'status',
        'queue',
        'job',
        'job_id,',
    ];

    protected static function newFactory(): JobStateFactory
    {
        return JobStateFactory::new();
    }

    protected function casts()
    {
        return [
            'status' => JobStateValue::class,
        ];
    }

    public function subject(): MorphTo
    {
        return $this->morphTo();
    }
}
