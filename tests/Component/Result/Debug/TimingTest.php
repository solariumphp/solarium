<?php

namespace Solarium\Tests\Component\Result\Debug;

use PHPUnit\Framework\TestCase;
use Solarium\Component\Result\Debug\Timing;
use Solarium\Component\Result\Debug\TimingPhase;

class TimingTest extends TestCase
{
    protected Timing $result;

    protected float $time;

    /**
     * @var TimingPhase[]
     */
    protected array $phases;

    public function setUp(): void
    {
        $this->time = 14;
        $this->phases = [
            'key1' => new TimingPhase('dummy1', 1, []),
            'key2' => new TimingPhase('dummy2', 3, []),
        ];
        $this->result = new Timing($this->time, $this->phases);
    }

    public function testGetTime(): void
    {
        $this->assertEquals(
            $this->time,
            $this->result->getTime()
        );
    }

    public function testGetPhase(): void
    {
        $this->assertEquals(
            $this->phases['key1'],
            $this->result->getPhase('key1')
        );
    }

    public function testGetPhaseWithInvalidKey(): void
    {
        $this->assertNull(
            $this->result->getPhase('invalidkey')
        );
    }

    public function testGetPhases(): void
    {
        $this->assertEquals(
            $this->phases,
            $this->result->getPhases()
        );
    }

    public function testIterator(): void
    {
        $items = [];
        foreach ($this->result as $key => $item) {
            $items[$key] = $item;
        }

        $this->assertEquals($this->phases, $items);
    }

    public function testCount(): void
    {
        $this->assertSameSize($this->phases, $this->result);
    }
}
