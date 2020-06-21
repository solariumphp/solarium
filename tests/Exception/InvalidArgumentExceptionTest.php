<?php

namespace Solarium\Tests\Exception;

use PHPUnit\Framework\TestCase;
use Solarium\Exception\InvalidArgumentException;

class InvalidArgumentExceptionTest extends TestCase
{
    public function testException()
    {
        $this->expectException('Solarium\Exception\InvalidArgumentException');
        throw new InvalidArgumentException();
    }

    public function testSPLException()
    {
        $this->expectException('\InvalidArgumentException');
        throw new InvalidArgumentException();
    }

    public function testSPLParentException()
    {
        $this->expectException('\LogicException');
        throw new InvalidArgumentException();
    }

    public function testLogicMarkerInterface()
    {
        $this->expectException('Solarium\Exception\LogicExceptionInterface');
        throw new InvalidArgumentException();
    }

    public function testMarkerInterface()
    {
        $this->expectException('Solarium\Exception\ExceptionInterface');
        throw new InvalidArgumentException();
    }
}
