<?php

namespace Solarium\Tests\Exception;

use PHPUnit\Framework\TestCase;
use Solarium\Exception\DomainException;

class DomainExceptionTest extends TestCase
{
    public function testException()
    {
        $this->expectException('Solarium\Exception\DomainException');
        throw new DomainException();
    }

    public function testSPLException()
    {
        $this->expectException('\DomainException');
        throw new DomainException();
    }

    public function testSPLParentException()
    {
        $this->expectException('\LogicException');
        throw new DomainException();
    }

    public function testLogicMarkerInterface()
    {
        $this->expectException('Solarium\Exception\LogicExceptionInterface');
        throw new DomainException();
    }

    public function testMarkerInterface()
    {
        $this->expectException('Solarium\Exception\ExceptionInterface');
        throw new DomainException();
    }
}
