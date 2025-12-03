<?php

namespace Solarium\Tests\QueryType\Select\Result\Grouping;

use PHPUnit\Framework\TestCase;
use Solarium\Component\Result\Grouping\FieldGroup;
use Solarium\Component\Result\Grouping\Result;

class GroupingTest extends TestCase
{
    protected Result $grouping;

    protected array $items;

    public function setUp(): void
    {
        $this->items = [
            'key1' => new FieldGroup(12, 6, []),
            'key2' => new FieldGroup(18, 3, []),
        ];

        $this->grouping = new Result($this->items);
    }

    public function testGetGroups(): void
    {
        $this->assertSame($this->items, $this->grouping->getGroups());
    }

    public function testGetGroup(): void
    {
        $this->assertSame($this->items['key1'], $this->grouping->getGroup('key1'));
    }

    public function testGetGroupInvalid(): void
    {
        $this->assertNull($this->grouping->getGroup('invalidkey'));
    }

    public function testIterator(): void
    {
        $items = [];
        foreach ($this->grouping as $key => $item) {
            $items[$key] = $item;
        }

        $this->assertSame($this->items, $items);
    }

    public function testCount(): void
    {
        $this->assertSameSize($this->items, $this->grouping);
    }
}
