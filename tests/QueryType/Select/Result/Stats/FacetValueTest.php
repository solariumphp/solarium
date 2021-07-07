<?php

namespace Solarium\Tests\QueryType\Select\Result\Stats;

use PHPUnit\Framework\TestCase;
use Solarium\Component\Result\Stats\FacetValue;

class FacetValueTest extends TestCase
{
    /**
     * @var FacetValue
     */
    protected $result;

    protected $value;

    protected $stats;

    public function setUp(): void
    {
        $this->value = 'myvalue';
        $this->stats = [
            'min' => 0.1,
            'max' => 3.5,
            'sum' => 3.333,
            'count' => -1,
            'missing' => 0,
            'sumOfSquares' => 17.23,
            'mean' => 3.14,
            'stddev' => 1.11,
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
            'dummy' => 'test',
        ];

        $this->result = new FacetValue($this->value, $this->stats);
    }

    public function testGetValue()
    {
        $this->assertSame($this->value, $this->result->getValue());
    }

    public function testGetMin()
    {
        $this->assertSame($this->stats['min'], $this->result->getMin());
    }

    public function testGetMax()
    {
        $this->assertSame($this->stats['max'], $this->result->getMax());
    }

    public function testGetSum()
    {
        $this->assertSame($this->stats['sum'], $this->result->getSum());
    }

    public function testGetCount()
    {
        $this->assertSame($this->stats['count'], $this->result->getCount());
    }

    public function testGetMissing()
    {
        $this->assertSame($this->stats['missing'], $this->result->getMissing());
    }

    public function testGetSumOfSquares()
    {
        $this->assertSame($this->stats['sumOfSquares'], $this->result->getSumOfSquares());
    }

    public function testGetMean()
    {
        $this->assertSame($this->stats['mean'], $this->result->getMean());
    }

    public function testGetStddev()
    {
        $this->assertSame($this->stats['stddev'], $this->result->getStddev());
    }

    public function testGetPercentiles()
    {
        $this->assertSame($this->stats['percentiles'], $this->result->getPercentiles());
    }

    public function testGetDistinctValues()
    {
        $this->assertSame($this->stats['distinctValues'], $this->result->getDistinctValues());
    }

    public function testGetCountDistinct()
    {
        $this->assertSame($this->stats['countDistinct'], $this->result->getCountDistinct());
    }

    public function testGetCardinality()
    {
        $this->assertSame($this->stats['cardinality'], $this->result->getCardinality());
    }

    /**
     * @deprecated Will be removed in Solarium 7
     */
    public function testGetFacets()
    {
        $this->assertNull($this->result->getFacets());
    }

    public function testGetStatValue()
    {
        $this->assertSame($this->stats['dummy'], $this->result->getStatValue('dummy'));
    }

    public function testGetStatValueUnknown()
    {
        $this->assertNull($this->result->getStatValue('unknown'));
    }
}
