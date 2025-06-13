<?php

namespace Solarium\Tests\QueryType\Update;

use PHPUnit\Framework\TestCase;

abstract class AbstractResultTestCase extends TestCase
{
    protected $result;

    public function testGetStatus(): void
    {
        $this->assertSame(
            1,
            $this->result->getStatus()
        );
    }

    public function testGetQueryTime(): void
    {
        $this->assertSame(
            12,
            $this->result->getQueryTime()
        );
    }
}
