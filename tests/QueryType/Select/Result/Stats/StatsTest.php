<?php

namespace Solarium\Tests\QueryType\Select\Result\Stats;

use PHPUnit\Framework\TestCase;
use Solarium\Component\Result\Stats\Result;
use Solarium\Component\Result\Stats\Stats;

class StatsTest extends TestCase
{
    protected $data;

    /**
     * @var Stats
     */
    protected $stats;

    public function setUp(): void
    {
        $this->data = [
            'key1' => new Result('field1', ['mean' => 2.72]),
            'key2' => new Result('field2', ['mean' => 3.14]),
        ];
        $this->stats = new Stats($this->data);
    }

    public function testGetResult()
    {
        $this->assertSame('field1', $this->stats->getResult('key1')->getName());
        $this->assertSame(2.72, $this->stats->getResult('key1')->getMean());
    }

    public function testGetInvalidResult()
    {
        $this->assertNull($this->stats->getResult('key3'));
    }

    public function testSetResult()
    {
        $this->stats->setResult('key1', new Result('field3', ['mean' => 42.0]));

        $this->assertSame('field3', $this->stats->getResult('key1')->getName());
        $this->assertSame(42.0, $this->stats->getResult('key1')->getMean());
    }

    public function testRemoveResult()
    {
        $this->stats->removeResult('key1');
        $this->assertNull($this->stats->getResult('key1'));
    }

    public function testGetResults()
    {
        $this->assertSame($this->data, $this->stats->getResults());
    }

    public function testIterator()
    {
        $items = [];
        foreach ($this->stats as $key => $item) {
            $items[$key] = $item;
        }

        $this->assertSame($this->data, $items);
    }

    public function testCount()
    {
        $this->assertCount(count($this->data), $this->stats);
    }
}
