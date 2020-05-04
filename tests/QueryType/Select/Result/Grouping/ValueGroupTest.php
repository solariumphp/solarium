<?php

namespace Solarium\Tests\QueryType\Select\Result\Grouping;

use PHPUnit\Framework\TestCase;
use Solarium\Component\Result\Grouping\ValueGroup;

class ValueGroupTest extends TestCase
{
    /**
     * @var ValueGroup
     */
    protected $group;

    protected $value;

    protected $numFound;

    protected $start;

    protected $items;

    public function setUp(): void
    {
        $this->value = 'test value';
        $this->numFound = 6;
        $this->start = 2;

        $this->items = [
            'key1' => 'content1',
            'key2' => 'content2',
        ];

        $this->group = new ValueGroup($this->value, $this->numFound, $this->start, $this->items);
    }

    public function testGetValue()
    {
        $this->assertSame($this->value, $this->group->getValue());
    }

    public function testGetNumFound()
    {
        $this->assertSame($this->numFound, $this->group->getNumFound());
    }

    public function testGetStart()
    {
        $this->assertSame($this->start, $this->group->getStart());
    }

    public function testGetDocuments()
    {
        $this->assertSame($this->items, $this->group->getDocuments());
    }

    public function testIterator()
    {
        $items = [];
        foreach ($this->group as $key => $item) {
            $items[$key] = $item;
        }

        $this->assertSame($this->items, $items);
    }

    public function testCount()
    {
        $this->assertCount(count($this->items), $this->group);
    }
}
