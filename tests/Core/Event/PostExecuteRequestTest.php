<?php

namespace Solarium\Tests\Core\Event;

use PHPUnit\Framework\TestCase;
use Solarium\Core\Client\Client;
use Solarium\Core\Client\Request;
use Solarium\Core\Client\Response;
use Solarium\Core\Event\PostExecuteRequest;

class PostExecuteRequestTest extends TestCase
{
    public function testConstructorAndGetters()
    {
        $client = new Client();
        $request = new Request();
        $request->addParam('testparam', 'test value');
        $endpoint = $client->getEndpoint();
        $response = new Response('', ['HTTP 1.0 200 OK']);

        $event = new PostExecuteRequest($request, $endpoint, $response);

        $this->assertSame($request, $event->getRequest());
        $this->assertSame($endpoint, $event->getEndpoint());
        $this->assertSame($response, $event->getResponse());
    }
}
