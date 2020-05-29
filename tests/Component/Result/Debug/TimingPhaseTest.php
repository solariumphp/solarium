<?php

namespace Solarium\Tests\Component\Result\Debug;

use PHPUnit\Framework\TestCase;
use Solarium\Component\Result\Debug\TimingPhase;

class TimingPhaseTest extends TestCase
{
    /**
     * @var TimingPhase
     */
    protected $result;

    protected $name;

    protected $time;

    protected $timings;

    public function setUp(): void
    {
        $this->name = 'dummy-name';
        $this->time = 14;
        $this->timings = ['class1' => 1, 'class2' => 3];
        $this->result = new TimingPhase($this->name, $this->time, $this->timings);
    }

    public function testGetTime()
    {
        $this->assertEquals(
            $this->time,
            $this->result->getTime()
        );
    }

    public function testGetTiming()
    {
        $this->assertEquals(
            $this->timings['class1'],
            $this->result->getTiming('class1')
        );
    }

    public function testGetPhaseWithInvalidKey()
    {
        $this->assertNull(
            $this->result->getTiming('invalidkey')
        );
    }

    public function testGetTimings()
    {
        $this->assertEquals(
            $this->timings,
            $this->result->getTimings()
        );
    }

    public function testIterator()
    {
        $items = [];
        foreach ($this->result as $key => $item) {
            $items[$key] = $item;
        }

        $this->assertEquals($this->timings, $items);
    }

    public function testCount()
    {
        $this->assertCount(count($this->timings), $this->result);
    }
}
