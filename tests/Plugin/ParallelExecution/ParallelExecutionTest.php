<?php

namespace Solarium\Tests\Plugin\ParallelExecution;

use PHPUnit\Framework\TestCase;
use Solarium\Core\Client\Adapter\ConnectionTimeoutAwareInterface;
use Solarium\Core\Client\Adapter\ConnectionTimeoutAwareTrait;
use Solarium\Core\Client\Adapter\Curl;
use Solarium\Core\Client\Adapter\Http;
use Solarium\Exception\RuntimeException;
use Solarium\Plugin\ParallelExecution\ParallelExecution;
use Solarium\Tests\Integration\TestClientFactory;

class ParallelExecutionTest extends TestCase
{
    /**
     * @var ParallelExecution
     */
    protected $plugin;

    public function setUp(): void
    {
        $this->plugin = new ParallelExecution();
    }

    public function testInitPlugin()
    {
        $client = TestClientFactory::createWithCurlAdapter();
        $plugin = $client->getPlugin('parallelexecution');

        $this->assertInstanceOf(ParallelExecution::class, $plugin);
    }

    public function testInitPluginTypeKeepsCurlAdapter()
    {
        $client = TestClientFactory::createWithCurlAdapter();
        $adapter = $client->getAdapter();
        $client->registerPlugin('parallelexecution', $this->plugin);

        $this->assertSame($adapter, $client->getAdapter());
    }

    public function testInitPluginTypeSetsCurlAdapter()
    {
        $client = TestClientFactory::createWithPsr18Adapter();
        $adapter = $client->getAdapter();
        $client->registerPlugin('parallelexecution', $this->plugin);

        $this->assertNotSame($adapter, $client->getAdapter());
        $this->assertInstanceOf(Curl::class, $client->getAdapter());
    }

    public function testInitPluginTypeKeepsTimeoutOptions()
    {
        $adapter = new TimeoutAndConnectionTimeoutAwareAdapter();
        $adapter->setTimeout(15);
        $adapter->setConnectionTimeout(5);

        $client = TestClientFactory::createWithPsr18Adapter();
        $client->setAdapter($adapter);
        $client->registerPlugin('parallelexecution', $this->plugin);

        $this->assertSame(15, $client->getAdapter()->getTimeOut());
        $this->assertSame(5, $client->getAdapter()->getConnectionTimeOut());
    }

    public function testAddAndGetQueries()
    {
        $client = TestClientFactory::createWithCurlAdapter();
        $client->clearEndpoints();
        $client->createEndpoint('local1');
        $endpoint2 = $client->createEndpoint('local2');

        $this->plugin->initPlugin($client, []);

        $query1 = $client->createSelect()->setQuery('test1');
        $query2 = $client->createSelect()->setQuery('test2');

        $this->plugin->addQuery(1, $query1);
        $this->plugin->addQuery(2, $query2, $endpoint2);

        $this->assertSame(
            [
                1 => ['query' => $query1, 'endpoint' => 'local1'],
                2 => ['query' => $query2, 'endpoint' => 'local2'],
            ],
            $this->plugin->getQueries()
        );
    }

    public function testClearQueries()
    {
        $client = TestClientFactory::createWithCurlAdapter();
        $this->plugin->initPlugin($client, []);

        $query1 = $client->createSelect()->setQuery('test1');
        $query2 = $client->createSelect()->setQuery('test2');

        $this->plugin->addQuery(1, $query1);
        $this->plugin->addQuery(2, $query2);
        $this->plugin->clearQueries();

        $this->assertSame(
            [],
            $this->plugin->getQueries()
        );
    }

    public function testExecuteWithUnsupportedAdapter()
    {
        $client = TestClientFactory::createWithCurlAdapter();
        $this->plugin->initPlugin($client, []);
        $client->setAdapter(new Http());

        $this->expectException(RuntimeException::class);
        $this->plugin->execute();
    }
}

class TimeoutAndConnectionTimeoutAwareAdapter extends Http implements ConnectionTimeoutAwareInterface
{
    use ConnectionTimeoutAwareTrait;
}
