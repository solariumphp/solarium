<?php

namespace Solarium\Tests\Plugin\BufferedAdd\Event;

use PHPUnit\Framework\TestCase;
use Solarium\Plugin\BufferedAdd\Event\PreCommit;

class PreCommitTest extends TestCase
{
    public function testConstructorAndGetters()
    {
        $buffer = [1, 2, 3];
        $overwrite = true;
        $softCommit = false;
        $waitSearcher = true;
        $expungeDeletes = false;

        $event = new PreCommit($buffer, $overwrite, $softCommit, $waitSearcher, $expungeDeletes);

        $this->assertSame($buffer, $event->getBuffer());
        $this->assertTrue($event->getOverwrite());
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
    public function testSetAndGetExpungeDeletes($event)
    {
        $event->setExpungeDeletes(true);
        $this->assertTrue($event->getExpungeDeletes());
    }

    /**
     * @depends testConstructorAndGetters
     *
     * @param PreCommit $event
     */
    public function testSetAndGetOverwrite($event)
    {
        $event->setOverwrite(false);
        $this->assertFalse($event->getOverwrite());
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
}
