<?php

namespace Solarium\Tests\Exception;

use PHPUnit\Framework\TestCase;
use Solarium\Exception\UnexpectedValueException;

class UnexpectedValueExceptionTest extends TestCase
{
    public function testException(): void
    {
        $this->expectException('Solarium\Exception\UnexpectedValueException');
        throw new UnexpectedValueException();
    }

    public function testSPLException(): void
    {
        $this->expectException('\UnexpectedValueException');
        throw new UnexpectedValueException();
    }

    public function testSPLParentException(): void
    {
        $this->expectException('\RuntimeException');
        throw new UnexpectedValueException();
    }

    public function testRuntimeMarkerInterface(): void
    {
        $this->expectException('Solarium\Exception\RuntimeExceptionInterface');
        throw new UnexpectedValueException();
    }

    public function testMarkerInterface(): void
    {
        $this->expectException('Solarium\Exception\ExceptionInterface');
        throw new UnexpectedValueException();
    }
}
