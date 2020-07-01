<?php

namespace Solarium\Tests\Exception;

use PHPUnit\Framework\TestCase;
use Solarium\Exception\OutOfBoundsException;

class OutOfBoundsExceptionTest extends TestCase
{
    public function testException()
    {
        $this->expectException('Solarium\Exception\OutOfBoundsException');
        throw new OutOfBoundsException();
    }

    public function testSPLException()
    {
        $this->expectException('\OutOfBoundsException');
        throw new OutOfBoundsException();
    }

    public function testSPLParentException()
    {
        $this->expectException('\RuntimeException');
        throw new OutOfBoundsException();
    }

    public function testRuntimeMarkerInterface()
    {
        $this->expectException('Solarium\Exception\RuntimeExceptionInterface');
        throw new OutOfBoundsException();
    }

    public function testMarkerInterface()
    {
        $this->expectException('Solarium\Exception\ExceptionInterface');
        throw new OutOfBoundsException();
    }
}
