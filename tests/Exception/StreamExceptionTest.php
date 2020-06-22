<?php

namespace Solarium\Tests\Exception;

use PHPUnit\Framework\TestCase;
use Solarium\Exception\StreamException;

class StreamExceptionTest extends TestCase
{
    public function testException()
    {
        $this->expectException('Solarium\Exception\StreamException');
        throw new StreamException();
    }

    public function testSPLException()
    {
        $this->expectException('\UnexpectedValueException');
        throw new StreamException();
    }

    public function testSPLParentException()
    {
        $this->expectException('\RuntimeException');
        throw new StreamException();
    }

    public function testRuntimeMarkerInterface()
    {
        $this->expectException('Solarium\Exception\RuntimeExceptionInterface');
        throw new StreamException();
    }

    public function testMarkerInterface()
    {
        $this->expectException('Solarium\Exception\ExceptionInterface');
        throw new StreamException();
    }

    public function testSetExpression()
    {
        $exception = new StreamException();
        $exception->setExpression('testexpression');

        $this->assertSame(
            'testexpression',
            $exception->getExpression()
        );
    }
}
