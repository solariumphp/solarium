<?php

namespace Solarium\Tests\QueryType\Ping;

use PHPUnit\Framework\TestCase;
use Solarium\QueryType\Ping\Result;

class ResultTest extends TestCase
{
    protected Result $result;

    public function setUp(): void
    {
        $this->result = new PingDummy();
    }

    public function testGetPingStatus(): void
    {
        $this->assertSame(
            'OK',
            $this->result->getPingStatus()
        );
    }

    public function testGetZkConnected(): void
    {
        $this->assertTrue(
            $this->result->getZkConnected()
        );
    }

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

class PingDummy extends Result
{
    protected bool $parsed = true;

    public function __construct()
    {
        $this->status = 'OK';
        $this->responseHeader = ['zkConnected' => true, 'status' => 1, 'QTime' => 12];
    }
}
