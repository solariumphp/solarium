<?php

namespace Solarium\Tests\QueryType\Analysis\Result;

use PHPUnit\Framework\TestCase;
use Solarium\QueryType\Analysis\Result\Field;

class FieldTest extends TestCase
{
    /**
     * @var FieldDummy
     */
    protected $result;

    protected $items;

    public function setUp(): void
    {
        $this->items = ['key1' => 'dummy1', 'key2' => 'dummy2', 'key3' => 'dummy3'];
        $this->result = new FieldDummy(1, 12, $this->items);
    }

    public function testGetLists()
    {
        $this->assertSame($this->items, $this->result->getLists());
    }

    public function testCount()
    {
        $this->assertCount(count($this->items), $this->result);
    }

    public function testIterator()
    {
        $lists = [];
        foreach ($this->result as $key => $list) {
            $lists[$key] = $list;
        }

        $this->assertSame($this->items, $lists);
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

class FieldDummy extends Field
{
    protected $parsed = true;

    public function __construct($status, $queryTime, $items)
    {
        $this->items = $items;
        $this->responseHeader = ['status' => $status, 'QTime' => $queryTime];
    }
}
