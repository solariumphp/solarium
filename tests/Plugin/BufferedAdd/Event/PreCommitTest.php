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
        $this->assertSame($overwrite, $event->getOverwrite());
        $this->assertSame($softCommit, $event->getSoftCommit());
        $this->assertSame($waitSearcher, $event->getWaitSearcher());
        $this->assertSame($expungeDeletes, $event->getExpungeDeletes());

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
        $expungeDeletes = true;
        $event->setExpungeDeletes($expungeDeletes);
        $this->assertSame($expungeDeletes, $event->getExpungeDeletes());
    }

    /**
     * @depends testConstructorAndGetters
     *
     * @param PreCommit $event
     */
    public function testSetAndGetOverwrite($event)
    {
        $overwrite = false;
        $event->setOverwrite($overwrite);
        $this->assertSame($overwrite, $event->getOverwrite());
    }

    /**
     * @depends testConstructorAndGetters
     *
     * @param PreCommit $event
     */
    public function testSetAndGetSoftCommit($event)
    {
        $softCommit = true;
        $event->setSoftCommit($softCommit);
        $this->assertSame($softCommit, $event->getSoftCommit());
    }

    /**
     * @depends testConstructorAndGetters
     *
     * @param PreCommit $event
     */
    public function testSetAndGetWaitSearcher($event)
    {
        $waitSearcher = false;
        $event->setWaitSearcher($waitSearcher);
        $this->assertSame($waitSearcher, $event->getWaitSearcher());
    }
}
