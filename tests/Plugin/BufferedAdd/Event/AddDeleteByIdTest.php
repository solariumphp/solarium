<?php

namespace Solarium\Tests\Plugin\BufferedAdd\Event;

use PHPUnit\Framework\TestCase;
use Solarium\Plugin\BufferedAdd\Delete\Id;
use Solarium\Plugin\BufferedAdd\Event\AddDeleteById;

class AddDeleteByIdTest extends TestCase
{
    public function testConstructorAndGetter()
    {
        $event = new AddDeleteById(new Id(123));
        $this->assertSame(123, $event->getId());

        $event = new AddDeleteById(new Id('abc'));
        $this->assertSame('abc', $event->getId());

        return $event;
    }

    /**
     * @depends testConstructorAndGetter
     *
     * @param AddDeleteById $event
     */
    public function testSetAndGetId($event)
    {
        $event->setId(456);
        $this->assertSame(456, $event->getId());

        $event->setId('def');
        $this->assertSame('def', $event->getId());
    }
}
