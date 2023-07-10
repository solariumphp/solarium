<?php

namespace Solarium\Tests\Integration;

use PHPUnit\Framework\TestCase;
use Solarium\Core\Client\ClientInterface;
use Solarium\Core\Client\Request;

/**
 * Test connection reuse with PSR-18 adapter.
 *
 * @group integration
 */
class ConnectionReuseTest extends TestCase
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
     * Original org.eclipse.jetty.io.AbstractConnection log level to restore after the testcase.
     *
     * @var string
     */
    protected static $origLogLevel;

    /**
     * Original watcher threshold to restore after the testcase.
     *
     * @var string
     */
    protected static $origThreshold;

    /**
     * Keep track of the last retrieved logging timestamp.
     *
     * @var int
     */
    protected static $since = 0;

    public static function setUpBeforeClass(): void
    {
        self::$config = [
            'endpoint' => [
                'localhost' => [
                    'host' => '127.0.0.1',
                    'port' => 8983,
                    'path' => '/',
                    'username' => 'solr',
                    'password' => 'SolrRocks',
                ],
            ],
        ];

        self::$client = TestClientFactory::createWithPsr18Adapter(self::$config);

        // get the current log level to restore afterwards to avoid excessive logging in other testcases
        $query = self::$client->createApi([
            'version' => Request::API_V2,
            'handler' => 'node/logging',
        ]);
        $result = self::$client->execute($query);
        $connectionLogger = array_values(array_filter(
            $result->getData()['loggers'],
            function (array $logger): bool {
                return 'org.eclipse.jetty.io.AbstractConnection' === $logger['name'];
            }
        ))[0];
        self::$origLogLevel = $connectionLogger['level'];

        // set the minimal log level that will provide us with enough information
        $query = self::$client->createApi([
            'version' => Request::API_V2,
            'handler' => 'node/logging',
        ]);
        $query->addParam('set', 'org.eclipse.jetty.io.AbstractConnection:DEBUG');
        $result = self::$client->execute($query);
        self::assertTrue($result->getWasSuccessful());

        // get the current watcher threshold to restore afterwards
        $query = self::$client->createApi([
            'version' => Request::API_V2,
            'handler' => 'node/logging',
        ]);
        $query->addParam('since', self::$since);
        $result = self::$client->execute($query);
        self::$origThreshold = $result->getData()['info']['threshold'];

        // set the watcher threshold to match the log level we need
        // get the initial timestamp to use for retrieving the logging history
        $query = self::$client->createApi([
            'version' => Request::API_V2,
            'handler' => 'node/logging',
        ]);
        $query->addParam('since', self::$since);
        $query->addParam('threshold', 'DEBUG');
        $result = self::$client->execute($query);
        self::$since = $result->getData()['info']['last'];
    }

    public static function tearDownAfterClass(): void
    {
        // restore the original log level and watcher threshold
        $query = self::$client->createApi([
            'version' => Request::API_V2,
            'handler' => 'node/logging',
        ]);
        $query->addParam('set', sprintf('org.eclipse.jetty.io.AbstractConnection:%s', self::$origLogLevel));
        $query->addParam('threshold', self::$origThreshold);
        $result = self::$client->execute($query);
        self::assertTrue($result->getWasSuccessful());
    }

    /**
     * @dataProvider createAdapterProvider
     */
    public function testConnectionReuse(string $createFunction, int $expectedCount)
    {
        // make sure the next logged timestamp is not on the same millisecond as self::$since
        usleep(1000);

        $client = TestClientFactory::$createFunction(self::$config);

        // the logging for 5 requests fits within the default logging watcher buffer size
        for ($i = 0; $i < 5; ++$i) {
            $query = $client->createApi([
                'version' => Request::API_V2,
                'handler' => 'node/system',
            ]);
            $client->execute($query);
        }

        $query = $client->createApi([
            'version' => Request::API_V2,
            'handler' => 'node/logging',
        ]);
        $query->addParam('since', self::$since);
        $result = $client->execute($query);
        $data = $result->getData();
        self::$since = $data['info']['last'];

        $connections = $this->extractConnections($data['history']['docs']);

        // The count is off-by-1 with an additional connection for one of the tests:
        // - the request for node/logging without reuse is included in the count without reuse in most tests in a workflow;
        // - if it isn't picked up the first time around, it shows up instead when we get the log with reuse.
        // The request for node/logging with reuse doesn't cause another additional connection because of the reuse.
        $this->assertContains(\count($connections), [$expectedCount, $expectedCount + 1]);
    }

    public function createAdapterProvider(): array
    {
        return [
            'without reuse' => ['createWithCurlAdapter', 5],
            'with reuse' => ['createWithPsr18Adapter', 1],
        ];
    }

    /**
     * Extracts connections from the logging history docs.
     *
     * Connections are identified by the org.eclipse.jetty.io.AbstractConnection
     * logger's message with format "onOpen {}".
     *
     * @param array $docs
     *
     * @return string[]
     */
    protected function extractConnections(array $docs): array
    {
        $connections = [];

        foreach ($docs as $doc) {
            if ('org.eclipse.jetty.io.AbstractConnection' === $doc['logger'] && str_starts_with($doc['message'], 'onOpen ')) {
                $connections[] = $doc['message'];
            }
        }

        return $connections;
    }
}
