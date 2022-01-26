<?php

namespace Solarium\Tests\Plugin\BufferedDelete\Event;

use PHPUnit\Framework\TestCase;
use Solarium\Plugin\BufferedDelete\Delete\Id;
use Solarium\Plugin\BufferedDelete\Event\AddDeleteById;

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
