<?php

namespace Solarium\Tests\Core\Event;

use PHPUnit\Framework\TestCase;
use Solarium\Core\Client\Request;
use Solarium\Core\Event\PostCreateRequest;
use Solarium\QueryType\Select\Query\Query;

class PostCreateRequestTest extends TestCase
{
    public function testConstructorAndGetters()
    {
        $query = new Query();
        $query->setQuery('test123');
        $request = new Request();
        $request->addParam('testparam', 'test value');

        $event = new PostCreateRequest($query, $request);

        $this->assertSame($query, $event->getQuery());
        $this->assertSame($request, $event->getRequest());
    }
}
