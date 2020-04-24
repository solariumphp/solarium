<?php

namespace Solarium\Tests\Component\Result\Debug;

use PHPUnit\Framework\TestCase;
use Solarium\Component\Result\Debug\Timing;
use Solarium\Component\Result\Debug\TimingPhase;

class TimingTest extends TestCase
{
    /**
     * @var Timing
     */
    protected $result;

    protected $time;

    protected $phases;

    public function setUp(): void
    {
        $this->time = 14;
        $this->phases = [
            'key1' => new TimingPhase('dummy1', null, []),
            'key2' => new TimingPhase('dummy2', null, []),
        ];
        $this->result = new Timing($this->time, $this->phases);
    }

    public function testGetTime()
    {
        $this->assertEquals(
            $this->time,
            $this->result->getTime()
        );
    }

    public function testGetPhase()
    {
        $this->assertEquals(
            $this->phases['key1'],
            $this->result->getPhase('key1')
        );
    }

    public function testGetPhaseWithInvalidKey()
    {
        $this->assertNull(
            $this->result->getPhase('invalidkey')
        );
    }

    public function testGetPhases()
    {
        $this->assertEquals(
            $this->phases,
            $this->result->getPhases()
        );
    }

    public function testIterator()
    {
        $items = [];
        foreach ($this->result as $key => $item) {
            $items[$key] = $item;
        }

        $this->assertEquals($this->phases, $items);
    }

    public function testCount()
    {
        $this->assertCount(count($this->phases), $this->result);
    }
}
