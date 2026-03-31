<?php

namespace Brainstud\LaravelJobTracker\Tests\Feature;

use Brainstud\LaravelJobTracker\JobState;
use PHPUnit\Framework\TestCase;

class ConvertExceptionTest extends TestCase
{
    public function test_convert_exception_includes_type(): void
    {
        $exception = new \RuntimeException('Something went wrong', 42);

        $result = JobState::convertException($exception);

        $this->assertArrayHasKey('type', $result);
        $this->assertSame(\RuntimeException::class, $result['type']);
    }

    public function test_convert_exception_returns_all_keys(): void
    {
        $exception = new \InvalidArgumentException('Bad input');

        $result = JobState::convertException($exception);

        $this->assertSame(\InvalidArgumentException::class, $result['type']);
        $this->assertSame('Bad input', $result['message']);
        $this->assertSame(0, $result['code']);
        $this->assertArrayHasKey('file', $result);
        $this->assertArrayHasKey('line', $result);
        $this->assertArrayHasKey('trace', $result);
    }
}
