<?php

namespace Solarium\Tests\Exception;

use PHPUnit\Framework\TestCase;
use Solarium\Exception\InvalidArgumentException;

class InvalidArgumentExceptionTest extends TestCase
{
    public function testException(): void
    {
        $this->expectException('Solarium\Exception\InvalidArgumentException');
        throw new InvalidArgumentException();
    }

    public function testSPLException(): void
    {
        $this->expectException('\InvalidArgumentException');
        throw new InvalidArgumentException();
    }

    public function testSPLParentException(): void
    {
        $this->expectException('\LogicException');
        throw new InvalidArgumentException();
    }

    public function testLogicMarkerInterface(): void
    {
        $this->expectException('Solarium\Exception\LogicExceptionInterface');
        throw new InvalidArgumentException();
    }

    public function testMarkerInterface(): void
    {
        $this->expectException('Solarium\Exception\ExceptionInterface');
        throw new InvalidArgumentException();
    }
}
