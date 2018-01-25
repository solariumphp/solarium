<?php

namespace Solarium\Tests\QueryType\Select\Result\Highlighting;

use PHPUnit\Framework\TestCase;
use Solarium\Component\Result\Highlighting\Highlighting;

class HighlightingTest extends TestCase
{
    /**
     * @var Highlighting
     */
    protected $result;

    protected $items;

    public function setUp()
    {
        $this->items = [
            'key1' => 'content1',
            'key2' => 'content2',
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
        $this->assertSame(count($this->items), count($this->result));
    }
}
