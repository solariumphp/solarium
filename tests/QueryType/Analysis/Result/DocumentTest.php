<?php

namespace Solarium\Tests\QueryType\Analysis\Result;

use PHPUnit\Framework\TestCase;
use Solarium\QueryType\Analysis\Result\Document;

class DocumentTest extends TestCase
{
    /**
     * @var DocumentDummy
     */
    protected $result;

    protected $items;

    public function setUp()
    {
        $this->items = ['key1' => 'dummy1', 'key2' => 'dummy2', 'key3' => 'dummy3'];
        $this->result = new DocumentDummy(1, 12, $this->items);
    }

    public function testGetDocuments()
    {
        $this->assertSame($this->items, $this->result->getDocuments());
    }

    public function testCount()
    {
        $this->assertSame(count($this->items), count($this->result));
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
        $this->queryTime = $queryTime;
        $this->status = $status;
    }
}
