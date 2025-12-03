<?php

namespace Solarium\Tests\QueryType\Select\Result\Grouping;

use PHPUnit\Framework\TestCase;
use Solarium\Component\Result\Grouping\ValueGroup;

class ValueGroupTest extends TestCase
{
    protected ValueGroup $group;

    protected string $value;

    protected int $numFound;

    protected int $start;

    protected array $items;

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

    public function testGetValue(): void
    {
        $this->assertSame($this->value, $this->group->getValue());
    }

    public function testGetNumFound(): void
    {
        $this->assertSame($this->numFound, $this->group->getNumFound());
    }

    public function testGetStart(): void
    {
        $this->assertSame($this->start, $this->group->getStart());
    }

    public function testGetDocuments(): void
    {
        $this->assertSame($this->items, $this->group->getDocuments());
    }

    public function testIterator(): void
    {
        $items = [];
        foreach ($this->group as $key => $item) {
            $items[$key] = $item;
        }

        $this->assertSame($this->items, $items);
    }

    public function testCount(): void
    {
        $this->assertSameSize($this->items, $this->group);
    }
}
