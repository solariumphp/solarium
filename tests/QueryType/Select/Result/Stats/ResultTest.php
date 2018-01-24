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

    public function setUp()
    {
        $this->field = 'myfield';
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

        $this->result = new Result($this->field, $this->stats);
    }

    public function testGetName()
    {
        $this->assertSame($this->field, $this->result->getName());
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
