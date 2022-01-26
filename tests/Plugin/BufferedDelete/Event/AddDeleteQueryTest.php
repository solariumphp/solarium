<?php

namespace Solarium\Tests\Plugin\BufferedDelete\Event;

use PHPUnit\Framework\TestCase;
use Solarium\Plugin\BufferedDelete\Delete\Query;
use Solarium\Plugin\BufferedDelete\Event\AddDeleteQuery;

class AddDeleteQueryTest extends TestCase
{
    public function testConstructorAndGetter()
    {
        $event = new AddDeleteQuery(new Query('cat:abc'));
        $this->assertSame('cat:abc', $event->getQuery());

        return $event;
    }

    /**
     * @depends testConstructorAndGetter
     *
     * @param AddDeleteQuery $event
     */
    public function testSetAndGetQuery($event)
    {
        $event->setQuery('cat:def');
        $this->assertSame('cat:def', $event->getQuery());
    }
}
