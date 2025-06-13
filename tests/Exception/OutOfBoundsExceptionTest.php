<?php

namespace Solarium\Tests\Exception;

use PHPUnit\Framework\TestCase;
use Solarium\Exception\OutOfBoundsException;

class OutOfBoundsExceptionTest extends TestCase
{
    public function testException(): void
    {
        $this->expectException('Solarium\Exception\OutOfBoundsException');
        throw new OutOfBoundsException();
    }

    public function testSPLException(): void
    {
        $this->expectException('\OutOfBoundsException');
        throw new OutOfBoundsException();
    }

    public function testSPLParentException(): void
    {
        $this->expectException('\RuntimeException');
        throw new OutOfBoundsException();
    }

    public function testRuntimeMarkerInterface(): void
    {
        $this->expectException('Solarium\Exception\RuntimeExceptionInterface');
        throw new OutOfBoundsException();
    }

    public function testMarkerInterface(): void
    {
        $this->expectException('Solarium\Exception\ExceptionInterface');
        throw new OutOfBoundsException();
    }
}
