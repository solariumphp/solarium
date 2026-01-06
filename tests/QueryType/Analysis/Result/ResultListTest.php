<?php

namespace Solarium\Tests\QueryType\Analysis\Result;

use PHPUnit\Framework\TestCase;
use Solarium\QueryType\Analysis\Result\ResultList;

class ResultListTest extends TestCase
{
    protected ResultList $result;

    protected array $items;

    protected string $name;

    public function setUp(): void
    {
        $this->name = 'testname';
        $this->items = ['key1' => 'dummy1', 'key2' => 'dummy2', 'key3' => 'dummy3'];
        $this->result = new ResultList($this->name, $this->items);
    }

    public function testGetItems(): void
    {
        $this->assertSame($this->items, $this->result->getItems());
    }

    public function testCount(): void
    {
        $this->assertSameSize($this->items, $this->result);
    }

    public function testIterator(): void
    {
        $lists = [];
        foreach ($this->result as $key => $list) {
            $lists[$key] = $list;
        }

        $this->assertSame($this->items, $lists);
    }

    public function testGetName(): void
    {
        $this->assertSame(
            $this->name,
            $this->result->getName()
        );
    }
}
