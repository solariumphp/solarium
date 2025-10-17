<?php

namespace Solarium\Tests\Exception;

use PHPUnit\Framework\TestCase;
use Solarium\Exception\StreamException;

class StreamExceptionTest extends TestCase
{
    public function testException(): void
    {
        $this->expectException('Solarium\Exception\StreamException');
        throw new StreamException();
    }

    public function testSPLException(): void
    {
        $this->expectException('\UnexpectedValueException');
        throw new StreamException();
    }

    public function testSPLParentException(): void
    {
        $this->expectException('\RuntimeException');
        throw new StreamException();
    }

    public function testRuntimeMarkerInterface(): void
    {
        $this->expectException('Solarium\Exception\RuntimeExceptionInterface');
        throw new StreamException();
    }

    public function testMarkerInterface(): void
    {
        $this->expectException('Solarium\Exception\ExceptionInterface');
        throw new StreamException();
    }

    public function testSetExpression(): void
    {
        $exception = new StreamException();
        $exception->setExpression('testexpression');

        $this->assertSame(
            'testexpression',
            $exception->getExpression()
        );
    }
}
