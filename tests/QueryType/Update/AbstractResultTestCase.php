<?php

namespace Solarium\Tests\QueryType\Update;

use PHPUnit\Framework\TestCase;
use Solarium\Core\Query\Result\QueryType as Result;

abstract class AbstractResultTestCase extends TestCase
{
    protected Result $result;

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
