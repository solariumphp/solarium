<?php

namespace Solarium\Tests\Core\Event;

use PHPUnit\Framework\TestCase;
use Solarium\Core\Event\PostCreateQuery;
use Solarium\QueryType\Select\Query\Query;

class PostCreateQueryTest extends TestCase
{
    public function testConstructorAndGetters()
    {
        $type = 'testtype';
        $options = ['key' => 'value'];
        $query = new Query();
        $query->setQuery('test123');

        $event = new PostCreateQuery($type, $options, $query);

        $this->assertSame($type, $event->getQueryType());
        $this->assertSame($options, $event->getOptions());
        $this->assertSame($query, $event->getQuery());
    }
}
