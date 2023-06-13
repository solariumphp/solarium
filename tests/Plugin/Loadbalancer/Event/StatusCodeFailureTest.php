<?php

namespace Solarium\Tests\Plugin\Loadbalancer\Event;

use PHPUnit\Framework\TestCase;
use Solarium\Core\Client\Response;
use Solarium\Plugin\Loadbalancer\Event\StatusCodeFailure;
use Solarium\Tests\Integration\TestClientFactory;

class StatusCodeFailureTest extends TestCase
{
    public function testConstructorAndGetters()
    {
        $client = TestClientFactory::createWithCurlAdapter();
        $endpoint = $client->getEndpoint();
        $response = new Response('test response');

        $event = new StatusCodeFailure($endpoint, $response);

        $this->assertSame($endpoint, $event->getEndpoint());
        $this->assertSame($response, $event->getResponse());
    }
}
