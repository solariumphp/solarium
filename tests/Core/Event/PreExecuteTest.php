<?php

namespace Solarium\Tests\Core\Event;

use PHPUnit\Framework\TestCase;
use Solarium\Core\Client\Response;
use Solarium\Core\Event\PreExecute;
use Solarium\Core\Query\Result\Result;
use Solarium\Tests\Integration\TestClientFactory;

class PreExecuteTest extends TestCase
{
    public function testConstructorAndGetters()
    {
        $client = TestClientFactory::createWithCurlAdapter();
        $query = $client->createSelect();
        $query->setQuery('test123');

        $event = new PreExecute($query);

        $this->assertSame($query, $event->getQuery());

        return $event;
    }

    /**
     * @depends testConstructorAndGetters
     *
     * @param PreExecute $event
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
