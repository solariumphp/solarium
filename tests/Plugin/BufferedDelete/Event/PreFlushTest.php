<?php

namespace Solarium\Tests\Plugin\BufferedDelete\Event;

use PHPUnit\Framework\TestCase;
use Solarium\Plugin\BufferedDelete\Event\PreFlush;

class PreFlushTest extends TestCase
{
    public function testConstructorAndGetters()
    {
        $buffer = [1, 2, 3];

        $event = new PreFlush($buffer);

        $this->assertSame($buffer, $event->getBuffer());

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
}
