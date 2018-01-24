<?php

namespace Solarium\Tests\Plugin\Loadbalancer\Event;

use PHPUnit\Framework\TestCase;
use Solarium\Core\Client\Client;
use Solarium\Exception\HttpException;
use Solarium\Plugin\Loadbalancer\Event\EndpointFailure;

class EndpointFailureTest extends TestCase
{
    public function testConstructorAndGetters()
    {
        $client = new Client();
        $endpoint = $client->getEndpoint();
        $httpException = new HttpException('test exception');

        $event = new EndpointFailure($endpoint, $httpException);

        $this->assertSame($endpoint, $event->getEndpoint());
        $this->assertSame($httpException, $event->getException());
    }
}
