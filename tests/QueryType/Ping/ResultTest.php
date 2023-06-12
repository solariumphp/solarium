<?php

namespace Solarium\Tests\QueryType\Ping;

use PHPUnit\Framework\TestCase;
use Solarium\QueryType\Ping\Result;

class ResultTest extends TestCase
{
    /**
     * @var Result
     */
    protected $result;

    public function setUp(): void
    {
        $this->result = new PingDummy();
    }

    public function testGetPingStatus()
    {
        $this->assertSame(
            'OK',
            $this->result->getPingStatus()
        );
    }

    public function testGetZkConnected()
    {
        $this->assertTrue(
            $this->result->getZkConnected()
        );
    }

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

class PingDummy extends Result
{
    protected $parsed = true;

    public function __construct()
    {
        $this->status = 'OK';
        $this->responseHeader = ['zkConnected' => true, 'status' => 1, 'QTime' => 12];
    }
}
