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

    public function setUp()
    {
        $this->value = 'myvalue';
        $this->stats = [
            'min' => 'dummyMin',
            'max' => 'dummyMax',
            'sum' => 'dummySum',
            'count' => 'dummyCount',
            'missing' => 'dummyMissing',
            'sumOfSquares' => 'dummySos',
            'mean' => 'dummyMean',
            'stddev' => 'dummyStddev',
            'facets' => 'dummyFacets',
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

    public function testGetFacets()
    {
        $this->assertSame($this->stats['facets'], $this->result->getFacets());
    }
}
