<?php

namespace Solarium\Tests\Integration\Proxy;

use Solarium\Core\Client\Adapter\ProxyAwareInterface;
use Solarium\Core\Client\Request;
use Solarium\Plugin\ParallelExecution\ParallelExecution;
use Solarium\QueryType\Server\Api\Result;
use Solarium\Tests\Integration\TestClientFactory;

/**
 * Test connecting through a proxy with the Curl adapter.
 *
 * @group integration
 * @group proxy
 */
class CurlTest extends AbstractProxyTestCase
{
    protected static function createClient(): void
    {
        self::$client = TestClientFactory::createWithCurlAdapter(self::$config);
    }

    protected static function setProxy(): void
    {
        self::assertInstanceOf(ProxyAwareInterface::class, self::$client->getAdapter());
        self::$client->getAdapter()->setProxy(sprintf('%s:%d', self::$proxy_server, self::$proxy_port));
    }

    public function testParallelConnections(): void
    {
        $query1 = self::$client->createApi([
            'version' => Request::API_V1,
            'handler' => 'admin/info/properties',
        ]);
        $query2 = self::$client->createApi([
            'version' => Request::API_V1,
            'handler' => 'admin/info/system',
        ]);

        /** @var ParallelExecution $parallel */
        $parallel = self::$client->getPlugin('parallelexecution');
        $parallel->addQuery('query1', $query1);
        $parallel->addQuery('query2', $query2);
        $results = $parallel->execute();

        $this->assertInstanceOf(Result::class, $results['query1']);
        $this->assertTrue($results['query1']->getWasSuccessful());
        $this->assertInstanceOf(Result::class, $results['query2']);
        $this->assertTrue($results['query2']->getWasSuccessful());

        self::$client->removePlugin('parallelexecution');
    }
}
