<?php

namespace Solarium\Tests\Core\Event;

use PHPUnit\Framework\TestCase;
use Solarium\Core\Client\Response;
use Solarium\Core\Event\PreCreateResult;
use Solarium\Core\Query\Result\Result;
use Solarium\Tests\Integration\TestClientFactory;

class PreCreateResultTest extends TestCase
{
    public function testConstructorAndGetters()
    {
        $client = TestClientFactory::createWithCurlAdapter();
        $query = $client->createSelect();
        $query->setQuery('test123');
        $response = new Response('', ['HTTP 1.0 200 OK']);

        $event = new PreCreateResult($query, $response);

        $this->assertSame($query, $event->getQuery());
        $this->assertSame($response, $event->getResponse());

        return $event;
    }

    /**
     * @depends testConstructorAndGetters
     *
     * @param PreCreateResult $event
     */
    public function testSetAndGetResult($event)
    {
        $client = TestClientFactory::createWithCurlAdapter();
        $query = $client->createSelect();
        $query->setQuery('test123');
        $response = new Response('', ['HTTP 1.0 200 OK']);

        $result = new Result($query, $response);
        $event->setResult($result);

        $this->assertSame($result, $event->getResult());
    }
}
