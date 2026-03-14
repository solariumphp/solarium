<?php

namespace Solarium\Tests\Integration\Proxy;

use PHPUnit\Framework\TestCase;
use Solarium\Client;
use Solarium\Core\Client\Adapter\ProxyAwareInterface;
use Solarium\Core\Client\Request;
use Solarium\QueryType\Server\Api\Result;

/**
 * Abstract test for connecting through a proxy.
 *
 * @group integration
 * @group proxy
 */
abstract class AbstractProxyTestCase extends TestCase
{
    protected static Client $client;

    protected static array $config;

    protected static string $proxy_server;

    protected static int $proxy_port;

    abstract protected static function createClient(): void;

    abstract protected static function setProxy(): void;

    public static function setUpBeforeClass(): void
    {
        self::$config = [
            'endpoint' => [
                'localhost' => [
                    'host' => 'solr',
                    'port' => 8983,
                    'path' => '/',
                    'username' => 'solr',
                    'password' => 'SolrRocks',
                ],
            ],
        ];
        self::$proxy_server = '127.0.0.1';
        self::$proxy_port = 8080;

        static::createClient();
        static::setProxy();
    }

    public function assertPreConditions(): void
    {
        $this->assertInstanceOf(
            ProxyAwareInterface::class,
            self::$client->getAdapter(),
            'Client adapter must implement ProxyAwareInterface!'
        );

        $this->assertNotNull(
            self::$client->getAdapter()->getProxy(),
            'Proxy test must set a proxy on the Client adapter!'
        );
    }

    public function testConnection(): void
    {
        $query = self::$client->createApi([
            'version' => Request::API_V1,
            'handler' => 'admin/info/system',
        ]);
        $result = self::$client->execute($query);

        $this->assertInstanceOf(Result::class, $result);
        $this->assertTrue($result->getWasSuccessful());
    }
}
