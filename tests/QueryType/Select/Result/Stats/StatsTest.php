<?php

namespace Solarium\Tests\QueryType\Select\Result\Stats;

use PHPUnit\Framework\TestCase;
use Solarium\Component\Result\Stats\Stats;

class StatsTest extends TestCase
{
    /**
     * @var Stats
     */
    protected $result;

    public function setUp()
    {
        $this->data = [
            'key1' => 'value1',
            'key2' => 'value2',
        ];
        $this->result = new Stats($this->data);
    }

    public function testGetResult()
    {
        $this->assertSame($this->data['key1'], $this->result->getResult('key1'));
    }

    public function testGetInvalidResult()
    {
        $this->assertNull($this->result->getResult('key3'));
    }

    public function testGetResults()
    {
        $this->assertSame($this->data, $this->result->getResults());
    }

    public function testIterator()
    {
        $items = [];
        foreach ($this->result as $key => $item) {
            $items[$key] = $item;
        }

        $this->assertSame($this->data, $items);
    }

    public function testCount()
    {
        $this->assertSame(count($this->data), count($this->result));
    }
}
