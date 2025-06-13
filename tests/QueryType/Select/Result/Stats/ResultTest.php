<?php

namespace Solarium\Tests\QueryType\Select\Result\Stats;

use PHPUnit\Framework\TestCase;
use Solarium\Component\Result\Stats\Result;

class ResultTest extends TestCase
{
    /**
     * @var Result
     */
    protected $result;

    protected $field;

    protected $stats;

    public function setUp(): void
    {
        $this->field = 'myfield';
        $this->stats = [
            'min' => 0.0,
            'max' => 1.0,
            'sum' => 4.2,
            'count' => -1,
            'missing' => 0,
            'sumOfSquares' => 1.41,
            'mean' => 3.14,
            'stddev' => 0.2,
            'percentiles' => [
                '50.0' => 3.14,
                '90.0' => 42.0,
            ],
            'distinctValues' => [
                'dummy1',
                'dummy2',
            ],
            'countDistinct' => 3,
            'cardinality' => 2,
            'facets' => ['dummyFacets'],
            'dummy' => 'test',
        ];

        $this->result = new Result($this->field, $this->stats);
    }

    public function testGetName(): void
    {
        $this->assertSame($this->field, $this->result->getName());
    }

    public function testGetMin(): void
    {
        $this->assertSame($this->stats['min'], $this->result->getMin());
    }

    public function testGetMax(): void
    {
        $this->assertSame($this->stats['max'], $this->result->getMax());
    }

    public function testGetSum(): void
    {
        $this->assertSame($this->stats['sum'], $this->result->getSum());
    }

    public function testGetCount(): void
    {
        $this->assertSame($this->stats['count'], $this->result->getCount());
    }

    public function testGetMissing(): void
    {
        $this->assertSame($this->stats['missing'], $this->result->getMissing());
    }

    public function testGetSumOfSquares(): void
    {
        $this->assertSame($this->stats['sumOfSquares'], $this->result->getSumOfSquares());
    }

    public function testGetMean(): void
    {
        $this->assertSame($this->stats['mean'], $this->result->getMean());
    }

    public function testGetStddev(): void
    {
        $this->assertSame($this->stats['stddev'], $this->result->getStddev());
    }

    public function testGetPercentiles(): void
    {
        $this->assertSame($this->stats['percentiles'], $this->result->getPercentiles());
    }

    public function testGetDistinctValues(): void
    {
        $this->assertSame($this->stats['distinctValues'], $this->result->getDistinctValues());
    }

    public function testGetCountDistinct(): void
    {
        $this->assertSame($this->stats['countDistinct'], $this->result->getCountDistinct());
    }

    public function testGetCardinality(): void
    {
        $this->assertSame($this->stats['cardinality'], $this->result->getCardinality());
    }

    public function testGetFacets(): void
    {
        $this->assertSame($this->stats['facets'], $this->result->getFacets());
    }

    public function testGetStatValue(): void
    {
        $this->assertSame($this->stats['dummy'], $this->result->getStatValue('dummy'));
    }

    public function testGetStatValueUnknown(): void
    {
        $this->assertNull($this->result->getStatValue('unknown'));
    }

    /**
     * Test stats that return a string value for string fields.
     */
    public function testStringStats(): void
    {
        $this->stats = [
            'min' => 'aaa',
            'max' => 'zzz',
        ];

        $this->result = new Result($this->field, $this->stats);

        $this->assertSame($this->stats['min'], $this->result->getMin());
        $this->assertSame($this->stats['max'], $this->result->getMax());
    }

    /**
     * Test stats that return a string value for date fields.
     */
    public function testDateStats(): void
    {
        $this->stats = [
            'min' => '2005-08-01T16:30:25Z',
            'max' => '2006-02-14T23:55:59Z',
            'mean' => '2006-01-15T12:49:38.727Z',
        ];

        $this->result = new Result($this->field, $this->stats);

        $this->assertSame($this->stats['min'], $this->result->getMin());
        $this->assertSame($this->stats['max'], $this->result->getMax());
        $this->assertSame($this->stats['mean'], $this->result->getMean());
    }

    /**
     * @deprecated
     */
    public function testGetValue(): void
    {
        $this->assertSame($this->stats['dummy'], $this->result->getValue('dummy'));
    }

    /**
     * @deprecated
     */
    public function testGetValueUnknown(): void
    {
        $this->assertNull($this->result->getValue('unknown'));
    }
}
