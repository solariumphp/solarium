<?php

namespace Solarium\Tests\Integration\Proxy;

use Solarium\Core\Client\Adapter\ProxyAwareInterface;
use Solarium\Tests\Integration\TestClientFactory;

/**
 * Test connecting through a proxy with the Http adapter.
 *
 * @group integration
 * @group proxy
 */
class HttpTest extends AbstractProxyTestCase
{
    protected static function createClient(): void
    {
        self::$client = TestClientFactory::createWithHttpAdapter(self::$config);
    }

    protected static function setProxy(): void
    {
        self::assertInstanceOf(ProxyAwareInterface::class, self::$client->getAdapter());
        self::$client->getAdapter()->setProxy(sprintf('%s:%d', self::$proxy_server, self::$proxy_port));
    }
}
