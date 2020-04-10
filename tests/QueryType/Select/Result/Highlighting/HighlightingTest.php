<?php

namespace Solarium\Tests\QueryType\Select\Result\Highlighting;

use PHPUnit\Framework\TestCase;
use Solarium\Component\Result\Highlighting\Highlighting;
use Solarium\Component\Result\Highlighting\Result;

class HighlightingTest extends TestCase
{
    /**
     * @var Highlighting
     */
    protected $result;

    protected $items;

    public function setUp(): void
    {
        $this->items = [
            'key1' => new Result(['content1']),
            'key2' => new Result(['content2']),
        ];

        $this->result = new Highlighting($this->items);
    }

    public function testGetResults()
    {
        $this->assertSame($this->items, $this->result->getResults());
    }

    public function testGetResult()
    {
        $this->assertSame(
            $this->items['key2'],
            $this->result->getResult('key2')
        );
    }

    public function testGetInvalidResult()
    {
        $this->assertNull(
            $this->result->getResult('invalid')
        );
    }

    public function testIterator()
    {
        $items = [];
        foreach ($this->result as $key => $item) {
            $items[$key] = $item;
        }

        $this->assertSame($this->items, $items);
    }

    public function testCount()
    {
        $this->assertCount(count($this->items), $this->result);
    }
}
