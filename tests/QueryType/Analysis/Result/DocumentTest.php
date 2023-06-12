<?php

namespace Solarium\Tests\QueryType\Analysis\Result;

use PHPUnit\Framework\TestCase;
use Solarium\QueryType\Analysis\Result\Document;
use Solarium\QueryType\Analysis\Result\ResultList;

class DocumentTest extends TestCase
{
    /**
     * @var DocumentDummy
     */
    protected $result;

    protected $items;

    public function setUp(): void
    {
        $this->items = [
            'key1' => new ResultList('dummy1', []),
            'key2' => new ResultList('dummy2', []),
            'key3' => new ResultList('dummy3', []),
        ];
        $this->result = new DocumentDummy(1, 12, $this->items);
    }

    public function testGetDocuments()
    {
        $this->assertSame($this->items, $this->result->getDocuments());
    }

    public function testCount()
    {
        $this->assertCount(count($this->items), $this->result);
    }

    public function testIterator()
    {
        $docs = [];
        foreach ($this->result as $key => $doc) {
            $docs[$key] = $doc;
        }

        $this->assertSame($this->items, $docs);
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

    public function testGetDocument()
    {
        $this->assertSame(
            $this->items['key2'],
            $this->result->getDocument('key2')
        );
    }

    public function testGetInvalidDocument()
    {
        $this->assertNull(
            $this->result->getDocument('invalidkey')
        );
    }
}

class DocumentDummy extends Document
{
    protected $parsed = true;

    public function __construct($status, $queryTime, $items)
    {
        $this->items = $items;
        $this->responseHeader = ['status' => $status, 'QTime' => $queryTime];
    }
}
