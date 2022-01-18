<?php

namespace Solarium\Tests\Plugin\BufferedDelete\Event;

use PHPUnit\Framework\TestCase;
use Solarium\Plugin\BufferedDelete\Event\PreCommit;

class PreCommitTest extends TestCase
{
    public function testConstructorAndGetters()
    {
        $buffer = [1, 2, 3];
        $softCommit = false;
        $waitSearcher = true;
        $expungeDeletes = false;

        $event = new PreCommit($buffer, $softCommit, $waitSearcher, $expungeDeletes);

        $this->assertSame($buffer, $event->getBuffer());
        $this->assertFalse($event->getSoftCommit());
        $this->assertTrue($event->getWaitSearcher());
        $this->assertFalse($event->getExpungeDeletes());

        return $event;
    }

    /**
     * @depends testConstructorAndGetters
     *
     * @param PreCommit $event
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
     * @param PreCommit $event
     */
    public function testSetAndGetSoftCommit($event)
    {
        $event->setSoftCommit(true);
        $this->assertTrue($event->getSoftCommit());
    }

    /**
     * @depends testConstructorAndGetters
     *
     * @param PreCommit $event
     */
    public function testSetAndGetWaitSearcher($event)
    {
        $event->setWaitSearcher(false);
        $this->assertFalse($event->getWaitSearcher());
    }

    /**
     * @depends testConstructorAndGetters
     *
     * @param PreCommit $event
     */
    public function testSetAndGetExpungeDeletes($event)
    {
        $event->setExpungeDeletes(true);
        $this->assertTrue($event->getExpungeDeletes());
    }
}
