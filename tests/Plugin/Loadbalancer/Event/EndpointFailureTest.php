<?php

namespace Solarium\Tests\Plugin\Loadbalancer\Event;

use PHPUnit\Framework\TestCase;
use Solarium\Exception\HttpException;
use Solarium\Plugin\Loadbalancer\Event\EndpointFailure;
use Solarium\Tests\Integration\TestClientFactory;

class EndpointFailureTest extends TestCase
{
    public function testConstructorAndGetters()
    {
        $client = TestClientFactory::createWithCurlAdapter();
        $endpoint = $client->getEndpoint();
        $httpException = new HttpException('test exception');

        $event = new EndpointFailure($endpoint, $httpException);

        $this->assertSame($endpoint, $event->getEndpoint());
        $this->assertSame($httpException, $event->getException());
    }
}
