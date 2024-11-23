<?php

namespace Brainstud\LaravelJobTracker\Tests\Data;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Label>
 */
class LabelFactory extends Factory
{
    protected $model = Label::class;

    public function definition(): array
    {
        return [
            'label' => fake()->word(),
        ];
    }
}
