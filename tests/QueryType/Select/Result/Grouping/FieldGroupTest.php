<?php

namespace Solarium\Tests\QueryType\Select\Result\Grouping;

use PHPUnit\Framework\TestCase;
use Solarium\Component\Result\Grouping\FieldGroup;

class FieldGroupTest extends TestCase
{
    /**
     * @var FieldGroup
     */
    protected $group;

    protected $matches;

    protected $numberOfGroups;

    protected $items;

    public function setUp(): void
    {
        $this->matches = 12;
        $this->numberOfGroups = 6;

        $this->items = [
            'key1' => 'content1',
            'key2' => 'content2',
        ];

        $this->group = new FieldGroup($this->matches, $this->numberOfGroups, $this->items);
    }

    public function testGetMatches()
    {
        $this->assertSame(
            $this->matches,
            $this->group->getMatches()
        );
    }

    public function testGetNumberOfGroups()
    {
        $this->assertSame(
            $this->numberOfGroups,
            $this->group->getNumberOfGroups()
        );
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
