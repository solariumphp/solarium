<?php

namespace Solarium\Tests\QueryType\Select\Result\Grouping;

use PHPUnit\Framework\TestCase;
use Solarium\Component\Result\Grouping\QueryGroup;

class QueryGroupTest extends TestCase
{
    /**
     * @var QueryGroup
     */
    protected $group;

    protected $matches;

    protected $numFound;

    protected $start;

    protected $maximumScore;

    protected $items;

    public function setUp(): void
    {
        $this->matches = 12;
        $this->numFound = 6;
        $this->start = 2;
        $this->maximumScore = 0.89;

        $this->items = [
            'key1' => 'content1',
            'key2' => 'content2',
        ];

        $this->group = new QueryGroup($this->matches, $this->numFound, $this->start, $this->maximumScore, $this->items);
    }

    public function testGetMatches()
    {
        $this->assertSame($this->matches, $this->group->getMatches());
    }

    public function testGetNumFound()
    {
        $this->assertSame($this->numFound, $this->group->getNumFound());
    }

    public function testGetStart()
    {
        $this->assertSame($this->start, $this->group->getStart());
    }

    public function testGetMaximumScore()
    {
        $this->assertSame($this->maximumScore, $this->group->getMaximumScore());
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
