<?php

namespace Brainstud\LaravelJobTracker\Tests\Data;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property string $label
 * @property Carbon $created_at
 * @property Carbon $updated_at
 */
class Label extends Model
{
    /** @use HasFactory<LabelFactory> */
    use HasFactory;

    protected $fillable = ['label'];

    protected static function newFactory(): LabelFactory
    {
        return LabelFactory::new();
    }

    // protected function casts()
    // {
    //     return [
    //         'created_at' => Carbon::class,
    //         'updated_at' => Carbon::class,
    //     ];
    // }
}
