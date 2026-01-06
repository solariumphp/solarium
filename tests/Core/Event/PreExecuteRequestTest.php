<?php

namespace Solarium\Tests\Core\Event;

use PHPUnit\Framework\TestCase;
use Solarium\Core\Client\Request;
use Solarium\Core\Client\Response;
use Solarium\Core\Event\PreExecuteRequest;
use Solarium\Tests\Integration\TestClientFactory;

class PreExecuteRequestTest extends TestCase
{
    public function testConstructorAndGetters(): PreExecuteRequest
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
     */
    public function testSetAndGetRequest(PreExecuteRequest $event): void
    {
        $request = new Request();
        $request->addParam('newparam', 'new value');

        $event->setRequest($request);
        $this->assertSame($request, $event->getRequest());
    }

    /**
     * @depends testConstructorAndGetters
     */
    public function testSetAndGetResponse(PreExecuteRequest $event): void
    {
        $response = new Response('', ['HTTP/1.0 200 OK']);
        $event->setResponse($response);
        $this->assertSame($response, $event->getResponse());
    }
}
