<?php

namespace Solarium\Tests\Plugin\BufferedAdd\Event;

use PHPUnit\Framework\TestCase;
use Solarium\Plugin\BufferedAdd\Event\PreFlush;

class PreFlushTest extends TestCase
{
    public function testConstructorAndGetters(): PreFlush
    {
        $buffer = [1, 2, 3];
        $commitWithin = 567;

        $event = new PreFlush($buffer, true, $commitWithin);

        $this->assertSame($buffer, $event->getBuffer());
        $this->assertTrue($event->getOverwrite());
        $this->assertSame($commitWithin, $event->getCommitWithin());

        return $event;
    }

    /**
     * @depends testConstructorAndGetters
     */
    public function testSetAndGetBuffer(PreFlush $event): void
    {
        $buffer = [4, 5, 6];
        $event->setBuffer($buffer);
        $this->assertSame($buffer, $event->getBuffer());
    }

    /**
     * @depends testConstructorAndGetters
     */
    public function testSetAndGetOverwrite(PreFlush $event): void
    {
        $event->setOverwrite(false);
        $this->assertFalse($event->getOverwrite());
    }

    /**
     * @depends testConstructorAndGetters
     */
    public function testSetAndGetCommitWithin(PreFlush $event): void
    {
        $commitWithin = 321;
        $event->setCommitWithin($commitWithin);
        $this->assertSame($commitWithin, $event->getCommitWithin());
    }
}
