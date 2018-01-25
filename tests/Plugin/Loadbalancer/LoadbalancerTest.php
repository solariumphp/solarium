<?php

namespace Solarium\Tests\Plugin\Loadbalancer;

use PHPUnit\Framework\TestCase;
use Solarium\Core\Client\Adapter\AdapterInterface;
use Solarium\Core\Client\Adapter\Http as HttpAdapter;
use Solarium\Core\Client\Client;
use Solarium\Core\Client\Endpoint;
use Solarium\Core\Client\Request;
use Solarium\Core\Event\PreCreateRequest as PreCreateRequestEvent;
use Solarium\Core\Event\PreExecuteRequest as PreExecuteRequestEvent;
use Solarium\Exception\HttpException;
use Solarium\Exception\InvalidArgumentException;
use Solarium\Plugin\Loadbalancer\Loadbalancer;
use Solarium\QueryType\Select\Query\Query as SelectQuery;
use Solarium\QueryType\Update\Query\Query as UpdateQuery;

class LoadbalancerTest extends TestCase
{
    /**
     * @var Loadbalancer
     */
    protected $plugin;

    /**
     * @var Client
     */
    protected $client;

    public function setUp()
    {
        $this->plugin = new Loadbalancer();

        $options = [
            'endpoint' => [
                'server1' => [
                    'host' => 'host1',
                ],
                'server2' => [
                    'host' => 'host2',
                ],
            ],
        ];

        $this->client = new Client($options);
        $adapter = $this->createMock(AdapterInterface::class);
        $adapter->expects($this->any())
            ->method('execute')
            ->willReturn('dummyresult');
        $this->client->setAdapter($adapter);
        $this->plugin->initPlugin($this->client, []);
    }

    public function testConfigMode()
    {
        $options = [
            'endpoint' => [
                'server1' => 10,
                'server2' => 5,
            ],
            'blockedquerytype' => [Client::QUERY_UPDATE, Client::QUERY_MORELIKETHIS],
        ];

        $this->plugin->setOptions($options);

        $this->assertSame(
            ['server1' => 10, 'server2' => 5],
            $this->plugin->getEndpoints()
        );

        $this->assertSame(
            [Client::QUERY_UPDATE, Client::QUERY_MORELIKETHIS],
            $this->plugin->getBlockedQueryTypes()
        );
    }

    public function testSetAndGetFailoverEnabled()
    {
        $this->plugin->setFailoverEnabled(true);
        $this->assertTrue($this->plugin->getFailoverEnabled());
    }

    public function testSetAndGetFailoverMaxRetries()
    {
        $this->plugin->setFailoverMaxRetries(16);
        $this->assertSame(16, $this->plugin->getFailoverMaxRetries());
    }

    public function testAddEndpoint()
    {
        $this->plugin->addEndpoint('s1', 10);

        $this->assertSame(
            ['s1' => 10],
            $this->plugin->getEndpoints()
        );
    }

    public function testAddEndpointWithObject()
    {
        $this->plugin->addEndpoint($this->client->getEndpoint('server1'), 10);

        $this->assertSame(
            ['server1' => 10],
            $this->plugin->getEndpoints()
        );
    }

    public function testAddEndpoints()
    {
        $endpoints = ['s1' => 10, 's2' => 8];
        $this->plugin->addEndpoints($endpoints);

        $this->assertSame(
            $endpoints,
            $this->plugin->getEndpoints()
        );
    }

    public function testAddEndpointWithDuplicateKey()
    {
        $this->plugin->addEndpoint('s1', 10);

        $this->expectException(InvalidArgumentException::class);
        $this->plugin->addEndpoint('s1', 20);
    }

    public function testClearEndpoints()
    {
        $this->plugin->addEndpoint('s1', 10);
        $this->plugin->clearEndpoints();
        $this->assertSame([], $this->plugin->getEndpoints());
    }

    public function testRemoveEndpoint()
    {
        $endpoints = [
            's1' => 10,
            's2' => 20,
        ];

        $this->plugin->addEndpoints($endpoints);
        $this->plugin->removeEndpoint('s1');

        $this->assertSame(
            ['s2' => 20],
            $this->plugin->getEndpoints()
        );
    }

    public function testRemoveEndpointWithObject()
    {
        $endpoints = [
            'server1' => 10,
            'server2' => 20,
        ];

        $this->plugin->addEndpoints($endpoints);
        $this->plugin->removeEndpoint($this->client->getEndpoint('server1'));

        $this->assertSame(
            ['server2' => 20],
            $this->plugin->getEndpoints()
        );
    }

    public function testSetEndpoints()
    {
        $endpoints1 = [
            's1' => 10,
            's2' => 20,
        ];

        $endpoints2 = [
            's3' => 50,
            's4' => 30,
        ];

        $this->plugin->addEndpoints($endpoints1);
        $this->plugin->setEndpoints($endpoints2);

        $this->assertSame(
            $endpoints2,
            $this->plugin->getEndpoints()
        );
    }

    public function testSetAndGetForcedEndpointForNextQuery()
    {
        $endpoints1 = [
            's1' => 10,
            's2' => 20,
        ];
        $this->plugin->addEndpoints($endpoints1);

        $this->plugin->setForcedEndpointForNextQuery('s2');
        $this->assertSame('s2', $this->plugin->getForcedEndpointForNextQuery());
    }

    public function testSetAndGetForcedEndpointForNextQueryWithObject()
    {
        $endpoints1 = [
            'server1' => 10,
            'server2' => 20,
        ];
        $this->plugin->addEndpoints($endpoints1);

        $this->plugin->setForcedEndpointForNextQuery($this->client->getEndpoint('server2'));
        $this->assertSame('server2', $this->plugin->getForcedEndpointForNextQuery());
    }

    public function testSetForcedEndpointForNextQueryWithInvalidKey()
    {
        $endpoints1 = [
            's1' => 10,
            's2' => 20,
        ];
        $this->plugin->addEndpoints($endpoints1);

        $this->expectException('Solarium\Exception\OutOfBoundsException');
        $this->plugin->setForcedEndpointForNextQuery('s3');
    }

    public function testAddBlockedQueryType()
    {
        $this->plugin->addBlockedQueryType('type1');
        $this->plugin->addBlockedQueryType('type2');

        $this->assertSame(
            [Client::QUERY_UPDATE, 'type1', 'type2'],
            $this->plugin->getBlockedQueryTypes()
        );
    }

    public function testClearBlockedQueryTypes()
    {
        $this->plugin->addBlockedQueryType('type1');
        $this->plugin->addBlockedQueryType('type2');
        $this->plugin->clearBlockedQueryTypes();
        $this->assertSame([], $this->plugin->getBlockedQueryTypes());
    }

    public function testAddBlockedQueryTypes()
    {
        $blockedQueryTypes = ['type1', 'type2', 'type3'];

        $this->plugin->clearBlockedQueryTypes();
        $this->plugin->addBlockedQueryTypes($blockedQueryTypes);
        $this->assertSame($blockedQueryTypes, $this->plugin->getBlockedQueryTypes());
    }

    public function testRemoveBlockedQueryType()
    {
        $blockedQueryTypes = ['type1', 'type2', 'type3'];

        $this->plugin->clearBlockedQueryTypes();
        $this->plugin->addBlockedQueryTypes($blockedQueryTypes);
        $this->plugin->removeBlockedQueryType('type2');

        $this->assertSame(
            ['type1', 'type3'],
            $this->plugin->getBlockedQueryTypes()
        );
    }

    public function testSetBlockedQueryTypes()
    {
        $blockedQueryTypes = ['type1', 'type2', 'type3'];

        $this->plugin->setBlockedQueryTypes($blockedQueryTypes);

        $this->assertSame(
            $blockedQueryTypes,
            $this->plugin->getBlockedQueryTypes()
        );
    }

    public function testPreExecuteRequestWithForcedEndpoint()
    {
        $endpoints = [
           'server1' => 100,
           'server2' => 1,
        ];
        $query = new SelectQuery();
        $request = new Request();

        $this->plugin->setEndpoints($endpoints);
        $this->plugin->setForcedEndpointForNextQuery('server2');

        $event = new PreCreateRequestEvent($query);
        $this->plugin->preCreateRequest($event);

        $event = new PreExecuteRequestEvent($request, new Endpoint());
        $this->plugin->preExecuteRequest($event);

        $this->assertSame(
            'server2',
            $this->plugin->getLastEndpoint()
        );
    }

    public function testDefaultEndpointRestore()
    {
        $originalHost = $this->client->getEndpoint()->getHost();
        $endpoints = [
           'server1' => 100,
           'server2' => 1,
        ];
        $request = new Request();

        $this->plugin->setEndpoints($endpoints);
        $this->plugin->setForcedEndpointForNextQuery('server2');

        $query = new SelectQuery();
        $event = new PreCreateRequestEvent($query);
        $this->plugin->preCreateRequest($event);

        $event = new PreExecuteRequestEvent($request, new Endpoint());
        $this->plugin->preExecuteRequest($event);

        $this->assertSame(
            'server2',
            $this->plugin->getLastEndpoint()
        );

        $query = new SelectQuery(); // this is a blocked querytype that should trigger a restore
        $event = new PreCreateRequestEvent($query);
        $this->plugin->preCreateRequest($event);

        $event = new PreExecuteRequestEvent($request, new Endpoint());
        $this->plugin->preExecuteRequest($event);

        $this->assertSame(
            $originalHost,
            $this->client->getEndpoint()->getHost()
        );
    }

    public function testBlockedQueryTypeNotLoadbalanced()
    {
        $originalHost = $this->client->getEndpoint()->getHost();
        $endpoints = [
           'server1' => 100,
           'server2' => 1,
        ];
        $this->plugin->setEndpoints($endpoints);
        $request = new Request();

        $query = new UpdateQuery(); // this is a blocked querytype that should not be loadbalanced
        $event = new PreCreateRequestEvent($query);
        $this->plugin->preCreateRequest($event);

        $event = new PreExecuteRequestEvent($request, new Endpoint());
        $this->plugin->preExecuteRequest($event);

        $this->assertSame(
            $originalHost,
            $this->client->getEndpoint()->getHost()
        );

        $this->assertNull(
            $this->plugin->getLastEndpoint()
        );
    }

    public function testLoadbalancerRandomizing()
    {
        $endpoints = [
           'server1' => 1,
           'server2' => 1,
        ];
        $this->plugin->setEndpoints($endpoints);
        $request = new Request();

        $query = new SelectQuery();
        $event = new PreCreateRequestEvent($query);
        $this->plugin->preCreateRequest($event);

        $event = new PreExecuteRequestEvent($request, new Endpoint());
        $this->plugin->preExecuteRequest($event);

        $this->assertTrue(
            in_array($this->plugin->getLastEndpoint(), ['server1', 'server2'], true)
        );
    }

    public function testFailover()
    {
        $this->plugin = new TestLoadbalancer(); // special loadbalancer that returns endpoints in fixed order
        $this->client->setAdapter(new TestAdapterForFailover()); // set special mock that fails once
        $this->plugin->initPlugin($this->client, []);

        $request = new Request();
        $endpoints = [
           'server1' => 1,
           'server2' => 1,
        ];
        $this->plugin->setEndpoints($endpoints);
        $this->plugin->setFailoverEnabled(true);

        $query = new SelectQuery();
        $event = new PreCreateRequestEvent($query);
        $this->plugin->preCreateRequest($event);

        $event = new PreExecuteRequestEvent($request, new Endpoint());
        $this->plugin->preExecuteRequest($event);

        $this->assertSame(
            'server2',
            $this->plugin->getLastEndpoint()
        );
    }

    public function testFailoverMaxRetries()
    {
        $this->plugin = new TestLoadbalancer(); // special loadbalancer that returns endpoints in fixed order

        $adapter = new TestAdapterForFailover();
        $adapter->setFailCount(10);
        $this->client->setAdapter($adapter); // set special mock that fails for all endpoints
        $this->plugin->initPlugin($this->client, []);

        $request = new Request();
        $endpoints = [
           'server1' => 1,
           'server2' => 1,
        ];
        $this->plugin->setEndpoints($endpoints);
        $this->plugin->setFailoverEnabled(true);

        $query = new SelectQuery();
        $event = new PreCreateRequestEvent($query);
        $this->plugin->preCreateRequest($event);

        $this->expectException(
            'Solarium\Exception\RuntimeException',
            'Maximum number of loadbalancer retries reached'
        );

        $event = new PreExecuteRequestEvent($request, new Endpoint());
        $this->plugin->preExecuteRequest($event);
    }
}

class TestLoadbalancer extends Loadbalancer
{
    protected $counter = 0;

    /**
     * Get options array for a randomized endpoint.
     *
     * @return array
     */
    protected function getRandomEndpoint()
    {
        ++$this->counter;
        $endpointKey = 'server'.$this->counter;

        $this->endpointExcludes[] = $endpointKey;
        $this->lastEndpoint = $endpointKey;

        return $this->client->getEndpoint($endpointKey);
    }
}

class TestAdapterForFailover extends HttpAdapter
{
    protected $counter = 0;

    protected $failCount = 1;

    public function setFailCount($count)
    {
        $this->failCount = $count;
    }

    public function execute($request, $endpoint)
    {
        ++$this->counter;
        if ($this->counter <= $this->failCount) {
            throw new HttpException('failover exception');
        }

        return 'dummyvalue';
    }
}
