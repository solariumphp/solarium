<?php

namespace Solarium\Tests\Exception;

use PHPUnit\Framework\TestCase;
use Solarium\Exception\RuntimeException;

class RuntimeExceptionTest extends TestCase
{
    public function testException()
    {
        $this->expectException('Solarium\Exception\RuntimeException');
        throw new RuntimeException();
    }

    public function testSPLException()
    {
        $this->expectException('\RuntimeException');
        throw new RuntimeException();
    }

    public function testRuntimeMarkerInterface()
    {
        $this->expectException('Solarium\Exception\RuntimeExceptionInterface');
        throw new RuntimeException();
    }

    public function testMarkerInterface()
    {
        $this->expectException('Solarium\Exception\ExceptionInterface');
        throw new RuntimeException();
    }
}
