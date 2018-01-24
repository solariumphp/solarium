<?php

namespace Solarium\Tests\QueryType\RealtimeGet;

use PHPUnit\Framework\TestCase;
use Solarium\QueryType\RealtimeGet\Result;
use Solarium\QueryType\Select\Result\Document;

class ResultTest extends TestCase
{
    protected $doc;

    protected $result;

    public function setUp()
    {
        $this->doc = new Document(['id' => 1, 'title' => 'doc1']);
        $this->result = new ResultDummy([$this->doc]);
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
    }
}
