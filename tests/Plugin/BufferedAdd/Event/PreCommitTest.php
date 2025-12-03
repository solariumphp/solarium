<?php

namespace Solarium\Tests\Plugin\BufferedAdd\Event;

use PHPUnit\Framework\TestCase;
use Solarium\Plugin\BufferedAdd\Event\PreCommit;

class PreCommitTest extends TestCase
{
    public function testConstructorAndGetters(): PreCommit
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
     */
    public function testSetAndGetBuffer(PreCommit $event): void
    {
        $buffer = [4, 5, 6];
        $event->setBuffer($buffer);
        $this->assertSame($buffer, $event->getBuffer());
    }

    /**
     * @depends testConstructorAndGetters
     */
    public function testSetAndGetOverwrite(PreCommit $event): void
    {
        $event->setOverwrite(false);
        $this->assertFalse($event->getOverwrite());
    }

    /**
     * @depends testConstructorAndGetters
     */
    public function testSetAndGetSoftCommit(PreCommit $event): void
    {
        $event->setSoftCommit(true);
        $this->assertTrue($event->getSoftCommit());
    }

    /**
     * @depends testConstructorAndGetters
     */
    public function testSetAndGetWaitSearcher(PreCommit $event): void
    {
        $event->setWaitSearcher(false);
        $this->assertFalse($event->getWaitSearcher());
    }

    /**
     * @depends testConstructorAndGetters
     */
    public function testSetAndGetExpungeDeletes(PreCommit $event): void
    {
        $event->setExpungeDeletes(true);
        $this->assertTrue($event->getExpungeDeletes());
    }
}
