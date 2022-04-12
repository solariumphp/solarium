<?php

namespace Solarium\Tests\Core\Event;

use PHPUnit\Framework\TestCase;
use Solarium\Core\Client\Request;
use Solarium\Core\Client\Response;
use Solarium\Core\Event\PreExecuteRequest;
use Solarium\Tests\Integration\TestClientFactory;

class PreExecuteRequestTest extends TestCase
{
    public function testConstructorAndGetters()
    {
        $client = TestClientFactory::createWithCurlAdapter();
        $request = new Request();
        $request->addParam('testparam', 'test value');
        $endpoint = $client->getEndpoint();

        $event = new PreExecuteRequest($request, $endpoint);

        $this->assertSame($request, $event->getRequest());
        $this->assertSame($endpoint, $event->getEndpoint());

        return $event;
    }

    /**
     * @depends testConstructorAndGetters
     *
     * @param PreExecuteRequest $event
     */
    public function testSetAndGetRequest($event)
    {
        $request = new Request();
        $request->addParam('newparam', 'new value');

        $event->setRequest($request);
        $this->assertSame($request, $event->getRequest());
    }

    /**
     * @depends testConstructorAndGetters
     *
     * @param PreExecuteRequest $event
     */
    public function testSetAndGetResponse($event)
    {
        $response = new Response('', ['HTTP 1.0 200 OK']);
        $event->setResponse($response);
        $this->assertSame($response, $event->getResponse());
    }
}
