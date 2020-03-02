<?php

namespace Solarium\Tests\Integration;

use Nyholm\Psr7\Factory\Psr17Factory;
use Solarium\Client;
use Http\Adapter\Guzzle6\Client as GuzzlePsrClient;
use Solarium\Core\Client\Adapter\Psr18Adapter;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

final class TestClientFactory
{
    public static function createWithPsr18Adapter(array $options = null, EventDispatcherInterface $eventDispatcher = null): Client
    {
        $factory = new Psr17Factory();

        return new Client(
            new Psr18Adapter(new GuzzlePsrClient(), $factory, $factory),
            $options,
            $eventDispatcher
        );
    }
}
