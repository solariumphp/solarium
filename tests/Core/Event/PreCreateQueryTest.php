<?php

namespace Solarium\Tests\Core\Event;

use PHPUnit\Framework\TestCase;
use Solarium\Core\Event\PreCreateQuery;
use Solarium\QueryType\Select\Query\Query;

class PreCreateQueryTest extends TestCase
{
    public function testConstructorAndGetters()
    {
        $type = 'testtype';
        $options = ['key' => 'value'];
        $query = new Query();
        $query->setQuery('test123');

        $event = new PreCreateQuery($type, $options);

        $this->assertSame($type, $event->getQueryType());
        $this->assertSame($options, $event->getOptions());

        return $event;
    }

    /**
     * @depends testConstructorAndGetters
     *
     * @param PreCreateQuery $event
     */
    public function testSetAndGetQuery($event)
    {
        $query = new Query();
        $query->setQuery('test123');

        $event->setQuery($query);

        $this->assertSame($query, $event->getQuery());
    }
}
