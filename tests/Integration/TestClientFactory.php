<?php

namespace Solarium\Tests\Integration;

use Http\Adapter\Guzzle6\Client as GuzzlePsrClient;
use Nyholm\Psr7\Factory\Psr17Factory;
use Solarium\Client;
use Solarium\Core\Client\Adapter\Curl;
use Solarium\Core\Client\Adapter\Psr18Adapter;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

final class TestClientFactory
{
    public static function createWithPsr18Adapter(array $options = null, EventDispatcherInterface $eventDispatcher = null): Client
    {
        $factory = new Psr17Factory();

        return new Client(
            new Psr18Adapter(new GuzzlePsrClient(), $factory, $factory),
            $eventDispatcher ?? new EventDispatcher(),
            $options
        );
    }

    public static function createWithCurlAdapter(array $options = null, EventDispatcherInterface $eventDispatcher = null): Client
    {
        return new Client(
            new Curl(),
            $eventDispatcher ?? new EventDispatcher(),
            $options
        );
    }
}
