<?php

namespace Solarium\Tests\Exception;

use PHPUnit\Framework\TestCase;
use Solarium\Exception\UnexpectedValueException;

class UnexpectedValueExceptionTest extends TestCase
{
    public function testException()
    {
        $this->expectException('Solarium\Exception\UnexpectedValueException');
        throw new UnexpectedValueException();
    }

    public function testSPLException()
    {
        $this->expectException('\UnexpectedValueException');
        throw new UnexpectedValueException();
    }

    public function testSPLParentException()
    {
        $this->expectException('\RuntimeException');
        throw new UnexpectedValueException();
    }

    public function testRuntimeMarkerInterface()
    {
        $this->expectException('Solarium\Exception\RuntimeExceptionInterface');
        throw new UnexpectedValueException();
    }

    public function testMarkerInterface()
    {
        $this->expectException('Solarium\Exception\ExceptionInterface');
        throw new UnexpectedValueException();
    }
}
