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
            'key1' => new Result('field1', ['value1']),
            'key2' => new Result('field2', ['value2']),
        ];
        $this->stats = new Stats($this->data);
    }

    public function testGetResult()
    {
        $this->assertSame('value1', $this->stats->getResult('key1')['stats'][0]);
    }

    public function testGetInvalidResult()
    {
        $this->assertNull($this->stats->getResult('key3'));
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
        $this->assertSame(count($this->data), count($this->stats));
    }
}
