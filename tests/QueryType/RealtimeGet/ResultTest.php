<?php

namespace Solarium\Tests\QueryType\RealtimeGet;

use PHPUnit\Framework\TestCase;
use Solarium\QueryType\RealtimeGet\Result;
use Solarium\QueryType\Select\Result\Document;

class ResultTest extends TestCase
{
    protected $doc;

    protected $result;

    public function setUp(): void
    {
        $this->doc = new Document(['id' => 1, 'title' => 'doc1']);
        $this->result = new ResultDummy([$this->doc]);
    }

    public function testGetStatus()
    {
        $this->assertSame(1, $this->result->getStatus());
    }

    public function testGetQueryTime()
    {
        $this->assertSame(12, $this->result->getQueryTime());
    }

    public function testGetDocument()
    {
        $this->assertSame($this->doc, $this->result->getDocument());
    }
}

class ResultDummy extends Result
{
    protected $parsed = true;

    public function __construct($docs)
    {
        $this->documents = $docs;
        $this->responseHeader = ['status' => 1, 'QTime' => 12];
    }
}
