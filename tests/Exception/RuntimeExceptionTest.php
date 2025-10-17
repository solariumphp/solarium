<?php

namespace Solarium\Tests\Exception;

use PHPUnit\Framework\TestCase;
use Solarium\Exception\RuntimeException;

class RuntimeExceptionTest extends TestCase
{
    public function testException(): void
    {
        $this->expectException('Solarium\Exception\RuntimeException');
        throw new RuntimeException();
    }

    public function testSPLException(): void
    {
        $this->expectException('\RuntimeException');
        throw new RuntimeException();
    }

    public function testRuntimeMarkerInterface(): void
    {
        $this->expectException('Solarium\Exception\RuntimeExceptionInterface');
        throw new RuntimeException();
    }

    public function testMarkerInterface(): void
    {
        $this->expectException('Solarium\Exception\ExceptionInterface');
        throw new RuntimeException();
    }
}
