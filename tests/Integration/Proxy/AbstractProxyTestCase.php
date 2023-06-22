<?php

namespace Solarium\Tests\Integration\Proxy;

use PHPUnit\Framework\TestCase;
use Solarium\Core\Client\ClientInterface;
use Solarium\Core\Client\Request;

/**
 * Abstract test for connecting through a proxy.
 *
 * @group integration
 */
abstract class AbstractProxyTestCase extends TestCase
{
    /**
     * @var ClientInterface
     */
    protected static $client;

    /**
     * @var array
     */
    protected static $config;

    /**
     * @var string
     */
    protected static $proxy_server;

    /**
     * @var int
     */
    protected static $proxy_port;

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

        self::assertNotNull(
            self::$client->getAdapter()->getProxy(),
            'Proxy test must set a proxy on the Client adapter!'
        );
    }

    public function testConnection()
    {
        $query = self::$client->createApi([
            'version' => Request::API_V1,
            'handler' => 'admin/info/system',
        ]);
        $result = self::$client->execute($query);

        $this->assertTrue($result->getWasSuccessful());
    }
}
