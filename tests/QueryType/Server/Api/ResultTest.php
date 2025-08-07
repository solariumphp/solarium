<?php

namespace Solarium\Tests\QueryType\Server\Api;

use PHPUnit\Framework\TestCase;
use Solarium\QueryType\Server\Api\Result;

class ResultTest extends TestCase
{
    protected $result;

    public function setUp(): void
    {
        $this->result = new ResultDummy();
    }

    public function testGetStatus(): void
    {
        $this->assertSame(1, $this->result->getStatus());
    }

    public function testGetWarning(): void
    {
        $expected = 'This response format is experimental.  It is likely to change in the future.';

        $this->assertSame($expected, $this->result->getWarning());
    }

    public function testGetData(): void
    {
        $expected = ['foo' => 'bar'];

        $this->assertSame($expected, $this->result->getData());
    }
}

class ResultDummy extends Result
{
    protected $parsed = true;

    public function __construct()
    {
        $this->WARNING = 'This response format is experimental.  It is likely to change in the future.';
        $this->data = ['foo' => 'bar'];
        $this->responseHeader = ['status' => 1, 'QTime' => 12];
    }
}
