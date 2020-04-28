<?php

namespace Solarium\Tests\QueryType\Select\Result\Facet;

use PHPUnit\Framework\TestCase;
use Solarium\Component\Result\Facet\Range;

class RangeTest extends TestCase
{
    /**
     * @var Range
     */
    protected $facet;

    protected $values;

    protected $before;

    protected $after;

    protected $between;

    protected $start;

    protected $end;

    protected $gap;

    public function setUp(): void
    {
        $this->values = [
            '10.0' => 12,
            '20.0' => 5,
            '30.0' => 3,
        ];

        $this->before = 2;
        $this->after = 4;
        $this->between = 3;
        $this->start = '10.0';
        $this->end = '40.0';
        $this->gap = '10.0';

        $this->facet = new Range(
            $this->values,
            $this->before,
            $this->after,
            $this->between,
            $this->start,
            $this->end,
            $this->gap
        );
    }

    public function testGetValues()
    {
        $this->assertSame($this->values, $this->facet->getValues());
    }

    public function testCount()
    {
        $this->assertCount(count($this->values), $this->facet);
    }

    public function testIterator()
    {
        $values = [];
        foreach ($this->facet as $key => $value) {
            $values[$key] = $value;
        }

        $this->assertSame($this->values, $values);
    }

    public function testGetBefore()
    {
        $this->assertSame($this->before, $this->facet->getBefore());
    }

    public function testGetAfter()
    {
        $this->assertSame($this->after, $this->facet->getAfter());
    }

    public function testGetBetween()
    {
        $this->assertSame($this->between, $this->facet->getBetween());
    }

    public function testGetStart()
    {
        $this->assertSame($this->start, $this->facet->getStart());
    }

    public function testGetEnd()
    {
        $this->assertSame($this->end, $this->facet->getEnd());
    }

    public function testGetGap()
    {
        $this->assertSame($this->gap, $this->facet->getGap());
    }
}
