<?php

namespace Solarium\Tests\Component\Result\Debug;

use PHPUnit\Framework\TestCase;
use Solarium\Component\Result\Debug\TimingPhase;

class TimingPhaseTest extends TestCase
{
    protected TimingPhase $result;

    protected string $name;

    protected float $time;

    /**
     * @var float[]
     */
    protected array $timings;

    public function setUp(): void
    {
        $this->name = 'dummy-name';
        $this->time = 14;
        $this->timings = ['class1' => 1, 'class2' => 3];
        $this->result = new TimingPhase($this->name, $this->time, $this->timings);
    }

    public function testGetTime(): void
    {
        $this->assertEquals(
            $this->time,
            $this->result->getTime()
        );
    }

    public function testGetTiming(): void
    {
        $this->assertEquals(
            $this->timings['class1'],
            $this->result->getTiming('class1')
        );
    }

    public function testGetPhaseWithInvalidKey(): void
    {
        $this->assertNull(
            $this->result->getTiming('invalidkey')
        );
    }

    public function testGetTimings(): void
    {
        $this->assertEquals(
            $this->timings,
            $this->result->getTimings()
        );
    }

    public function testIterator(): void
    {
        $items = [];
        foreach ($this->result as $key => $item) {
            $items[$key] = $item;
        }

        $this->assertEquals($this->timings, $items);
    }

    public function testCount(): void
    {
        $this->assertSameSize($this->timings, $this->result);
    }
}
