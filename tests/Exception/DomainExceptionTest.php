<?php

namespace Solarium\Tests\Exception;

use PHPUnit\Framework\TestCase;
use Solarium\Exception\DomainException;

class DomainExceptionTest extends TestCase
{
    public function testException(): void
    {
        $this->expectException('Solarium\Exception\DomainException');
        throw new DomainException();
    }

    public function testSPLException(): void
    {
        $this->expectException('\DomainException');
        throw new DomainException();
    }

    public function testSPLParentException(): void
    {
        $this->expectException('\LogicException');
        throw new DomainException();
    }

    public function testLogicMarkerInterface(): void
    {
        $this->expectException('Solarium\Exception\LogicExceptionInterface');
        throw new DomainException();
    }

    public function testMarkerInterface(): void
    {
        $this->expectException('Solarium\Exception\ExceptionInterface');
        throw new DomainException();
    }
}
