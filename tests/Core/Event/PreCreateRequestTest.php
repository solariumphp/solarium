<?php

namespace Solarium\Tests\Core\Event;

use PHPUnit\Framework\TestCase;
use Solarium\Core\Client\Request;
use Solarium\Core\Event\PreCreateRequest;
use Solarium\QueryType\Select\Query\Query;

class PreCreateRequestTest extends TestCase
{
    public function testConstructorAndGetters()
    {
        $query = new Query();
        $query->setQuery('test123');
        $event = new PreCreateRequest($query);
        $this->assertSame($query, $event->getQuery());

        return $event;
    }

    /**
     * @depends testConstructorAndGetters
     *
     * @param PreCreateRequest $event
     */
    public function testSetAndGetRequest($event)
    {
        $request = new Request();
        $request->addParam('testparam', 'test value');

        $event->setRequest($request);

        $this->assertSame($request, $event->getRequest());
    }
}
