<?php

namespace Solarium\Tests\Plugin\BufferedAdd\Event;

use PHPUnit\Framework\TestCase;
use Solarium\Plugin\BufferedAdd\Event\PreFlush;

class PreFlushTest extends TestCase
{
    public function testConstructorAndGetters()
    {
        $buffer = [1, 2, 3];
        $overwrite = true;
        $commitWithin = 567;

        $event = new PreFlush($buffer, $overwrite, $commitWithin);

        $this->assertSame($buffer, $event->getBuffer());
        $this->assertSame($overwrite, $event->getOverwrite());
        $this->assertSame($commitWithin, $event->getCommitWithin());

        return $event;
    }

    /**
     * @depends testConstructorAndGetters
     *
     * @param PreFlush $event
     */
    public function testSetAndGetBuffer($event)
    {
        $buffer = [4, 5, 6];
        $event->setBuffer($buffer);
        $this->assertSame($buffer, $event->getBuffer());
    }

    /**
     * @depends testConstructorAndGetters
     *
     * @param PreFlush $event
     */
    public function testSetAndGetCommitWithin($event)
    {
        $commitWithin = 321;
        $event->setCommitWithin($commitWithin);
        $this->assertSame($commitWithin, $event->getCommitWithin());
    }

    /**
     * @depends testConstructorAndGetters
     *
     * @param PreFlush $event
     */
    public function testSetAndGetOverwrite($event)
    {
        $overwrite = false;
        $event->setOverwrite($overwrite);
        $this->assertSame($overwrite, $event->getOverwrite());
    }
}
