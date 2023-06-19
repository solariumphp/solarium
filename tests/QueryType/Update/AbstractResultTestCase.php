<?php

namespace Solarium\Tests\QueryType\Update;

use PHPUnit\Framework\TestCase;

abstract class AbstractResultTestCase extends TestCase
{
    protected $result;

    public function testGetStatus()
    {
        $this->assertSame(
            1,
            $this->result->getStatus()
        );
    }

    public function testGetQueryTime()
    {
        $this->assertSame(
            12,
            $this->result->getQueryTime()
        );
    }
}
