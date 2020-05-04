<?php

namespace Solarium\Tests\QueryType\Analysis\Result;

use PHPUnit\Framework\TestCase;
use Solarium\QueryType\Analysis\Result\ResultList;

class ResultListTest extends TestCase
{
    /**
     * @var ResultList
     */
    protected $result;

    protected $items;

    protected $name;

    public function setUp(): void
    {
        $this->name = 'testname';
        $this->items = ['key1' => 'dummy1', 'key2' => 'dummy2', 'key3' => 'dummy3'];
        $this->result = new ResultList($this->name, $this->items);
    }

    public function testGetItems()
    {
        $this->assertSame($this->items, $this->result->getItems());
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

    public function testGetName()
    {
        $this->assertSame(
            $this->name,
            $this->result->getName()
        );
    }
}
