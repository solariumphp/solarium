<?php

namespace Solarium\Tests\Plugin\BufferedDelete\Event;

use PHPUnit\Framework\TestCase;
use Solarium\Plugin\BufferedDelete\Event\PreCommit;

class PreCommitTest extends TestCase
{
    public function testConstructorAndGetters(): PreCommit
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
