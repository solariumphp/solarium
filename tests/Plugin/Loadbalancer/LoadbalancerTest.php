<?php

namespace Solarium\Tests\Plugin\Loadbalancer;

use PHPUnit\Framework\TestCase;
use Solarium\Core\Client\Adapter\AdapterInterface;
use Solarium\Core\Client\Adapter\Http as HttpAdapter;
use Solarium\Core\Client\Client;
use Solarium\Core\Client\Endpoint;
use Solarium\Core\Client\Request;
use Solarium\Core\Client\Response;
use Solarium\Core\Event\Events;
use Solarium\Core\Event\PreCreateRequest as PreCreateRequestEvent;
use Solarium\Core\Event\PreExecuteRequest as PreExecuteRequestEvent;
use Solarium\Exception\HttpException;
use Solarium\Exception\InvalidArgumentException;
use Solarium\Exception\OutOfBoundsException;
use Solarium\Exception\RuntimeException;
use Solarium\Plugin\Loadbalancer\Event\EndpointFailure as EndpointFailureEvent;
use Solarium\Plugin\Loadbalancer\Event\Events as LoadbalancerEvents;
use Solarium\Plugin\Loadbalancer\Event\StatusCodeFailure as StatusCodeFailureEvent;
use Solarium\Plugin\Loadbalancer\Loadbalancer;
use Solarium\QueryType\Select\Query\Query as SelectQuery;
use Solarium\QueryType\Update\Query\Query as UpdateQuery;
use Solarium\Tests\Integration\TestClientFactory;
use Symfony\Component\EventDispatcher\EventDispatcher;

class LoadbalancerTest extends TestCase
{
    protected Loadbalancer $plugin;

    protected Client $client;

    /**
     * Query types that are blocked by default according to the documentation.
     */
    protected array $expectedDefaultBlockedQueryTypes = [
        Client::QUERY_UPDATE,
        Client::QUERY_EXTRACT,
    ];

    public function setUp(): void
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

        $this->client = TestClientFactory::createWithCurlAdapter($options);
        $adapter = $this->createMock(AdapterInterface::class);
        $adapter->expects($this->any())
            ->method('execute')
            ->willReturn(new Response('dummyresult', ['HTTP/1.1 200 OK']));
        $this->client->setAdapter($adapter);
        $this->plugin->initPlugin($this->client, []);
    }

    public function testConfigMode(): void
    {
        $options = [
            'failoverenabled' => true,
            'failovermaxretries' => 5,
            'failoverstatuscodes' => '402, 403',
            'endpoint' => [
                'server1' => 10,
                'server2' => 5,
            ],
            'blockedquerytype' => [Client::QUERY_UPDATE, Client::QUERY_MORELIKETHIS],
        ];

        $this->plugin->setOptions($options);

        $this->assertTrue($this->plugin->getFailoverEnabled());
        $this->assertSame(5, $this->plugin->getFailoverMaxRetries());
        $this->assertSame([402, 403], $this->plugin->getFailoverStatusCodes());

        $this->assertSame(
            ['server1' => 10, 'server2' => 5],
            $this->plugin->getEndpoints()
        );

        $this->assertSame(
            [Client::QUERY_UPDATE, Client::QUERY_MORELIKETHIS],
            $this->plugin->getBlockedQueryTypes()
        );
    }

    public function testInitPlugin(): Client
    {
        $client = TestClientFactory::createWithCurlAdapter();
        $plugin = $client->getPlugin('loadbalancer');
        /** @var EventDispatcher $eventDispatcher */
        $eventDispatcher = $client->getEventDispatcher();

        $this->assertInstanceOf(Loadbalancer::class, $plugin);

        $expectedListeners = [
            Events::PRE_EXECUTE_REQUEST => [
                [
                    $plugin,
                    'preExecuteRequest',
                ],
            ],
            Events::PRE_CREATE_REQUEST => [
                [
                    $plugin,
                    'preCreateRequest',
                ],
            ],
        ];

        $this->assertSame(
            $expectedListeners,
            $eventDispatcher->getListeners()
        );

        return $client;
    }

    /**
     * @depends testInitPlugin
     */
    public function testDeinitPlugin(Client $client): void
    {
        $client->removePlugin('loadbalancer');
        /** @var EventDispatcher $eventDispatcher */
        $eventDispatcher = $client->getEventDispatcher();

        $this->assertSame(
            [],
            $eventDispatcher->getListeners()
        );
    }

    public function testDefaultFailoverEnabled(): void
    {
        $this->assertFalse($this->plugin->getFailoverEnabled());
    }

    public function testSetAndGetFailoverEnabled(): void
    {
        $this->plugin->setFailoverEnabled(true);
        $this->assertTrue($this->plugin->getFailoverEnabled());
    }

    public function testSetAndGetFailoverMaxRetries(): void
    {
        $this->plugin->setFailoverMaxRetries(16);
        $this->assertSame(16, $this->plugin->getFailoverMaxRetries());
    }

    public function testDefaultFailoverStatusCodes(): void
    {
        $this->assertSame([], $this->plugin->getFailoverStatusCodes());
    }

    public function testAddFailoverStatusCode(): void
    {
        $this->plugin->addFailoverStatusCode(400);
        $this->plugin->addFailoverStatusCode(401);
        $this->assertSame([400, 401], $this->plugin->getFailoverStatusCodes());
    }

    public function testAddFailoverStatusCodes(): void
    {
        $this->plugin->addFailoverStatusCodes([400, 401]);
        $this->plugin->addFailoverStatusCodes('500, 501');
        $this->assertSame([400, 401, 500, 501], $this->plugin->getFailoverStatusCodes());
    }

    public function testClearFailoverStatusCodes(): void
    {
        $this->plugin->addFailoverStatusCode(400);
        $this->plugin->clearFailoverStatusCodes();
        $this->assertSame([], $this->plugin->getFailoverStatusCodes());
    }

    public function testRemoveFailoverStatusCode(): void
    {
        $this->plugin->addFailoverStatusCodes([400, 401]);
        $this->plugin->removeFailoverStatusCode(400);
        $this->assertSame([401], $this->plugin->getFailoverStatusCodes());
    }

    public function testSetFailoverStatusCodes(): void
    {
        $this->plugin->setFailoverStatusCodes([400, 401]);
        $this->assertSame([400, 401], $this->plugin->getFailoverStatusCodes());
        $this->plugin->setFailoverStatusCodes('500, 501');
        $this->assertSame([500, 501], $this->plugin->getFailoverStatusCodes());
    }

    public function testAddEndpoint(): void
    {
        $this->plugin->addEndpoint('s1', 10);

        $this->assertSame(
            ['s1' => 10],
            $this->plugin->getEndpoints()
        );
    }

    public function testAddEndpointWithObject(): void
    {
        $this->plugin->addEndpoint($this->client->getEndpoint('server1'), 10);

        $this->assertSame(
            ['server1' => 10],
            $this->plugin->getEndpoints()
        );
    }

    public function testAddEndpoints(): void
    {
        $endpoints = ['s1' => 10, 's2' => 8];
        $this->plugin->addEndpoints($endpoints);

        $this->assertSame(
            $endpoints,
            $this->plugin->getEndpoints()
        );
    }

    public function testAddEndpointWithDuplicateKey(): void
    {
        $this->plugin->addEndpoint('s1', 10);

        $this->expectException(InvalidArgumentException::class);
        $this->plugin->addEndpoint('s1', 20);
    }

    public function testClearEndpoints(): void
    {
        $this->plugin->addEndpoint('s1', 10);
        $this->plugin->clearEndpoints();
        $this->assertSame([], $this->plugin->getEndpoints());
    }

    public function testRemoveEndpoint(): void
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

    public function testRemoveEndpointWithObject(): void
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

    public function testSetEndpoints(): void
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

    public function testSetAndGetForcedEndpointForNextQuery(): void
    {
        $endpoints1 = [
            's1' => 10,
            's2' => 20,
        ];
        $this->plugin->addEndpoints($endpoints1);

        $this->plugin->setForcedEndpointForNextQuery('s2');
        $this->assertSame('s2', $this->plugin->getForcedEndpointForNextQuery());
    }

    public function testSetAndGetForcedEndpointForNextQueryWithObject(): void
    {
        $endpoints1 = [
            'server1' => 10,
            'server2' => 20,
        ];
        $this->plugin->addEndpoints($endpoints1);

        $this->plugin->setForcedEndpointForNextQuery($this->client->getEndpoint('server2'));
        $this->assertSame('server2', $this->plugin->getForcedEndpointForNextQuery());
    }

    public function testSetForcedEndpointForNextQueryWithInvalidKey(): void
    {
        $endpoints1 = [
            's1' => 10,
            's2' => 20,
        ];
        $this->plugin->addEndpoints($endpoints1);

        $this->expectException(OutOfBoundsException::class);
        $this->plugin->setForcedEndpointForNextQuery('s3');
    }

    public function testDefaultBlockedQueryTypes(): void
    {
        $this->assertEqualsCanonicalizing(
            $this->expectedDefaultBlockedQueryTypes,
            $this->plugin->getBlockedQueryTypes()
        );
    }

    public function testAddBlockedQueryType(): void
    {
        $this->plugin->addBlockedQueryType('type1');
        $this->plugin->addBlockedQueryType('type2');

        $this->assertEqualsCanonicalizing(
            array_merge($this->expectedDefaultBlockedQueryTypes, ['type1', 'type2']),
            $this->plugin->getBlockedQueryTypes()
        );
    }

    public function testClearBlockedQueryTypes(): void
    {
        $this->plugin->addBlockedQueryType('type1');
        $this->plugin->addBlockedQueryType('type2');
        $this->plugin->clearBlockedQueryTypes();
        $this->assertSame([], $this->plugin->getBlockedQueryTypes());
    }

    public function testAddBlockedQueryTypes(): void
    {
        $blockedQueryTypes = ['type1', 'type2', 'type3'];

        $this->plugin->clearBlockedQueryTypes();
        $this->plugin->addBlockedQueryTypes($blockedQueryTypes);
        $this->assertSame($blockedQueryTypes, $this->plugin->getBlockedQueryTypes());
    }

    public function testRemoveBlockedQueryType(): void
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

    public function testSetBlockedQueryTypes(): void
    {
        $blockedQueryTypes = ['type1', 'type2', 'type3'];

        $this->plugin->setBlockedQueryTypes($blockedQueryTypes);

        $this->assertSame(
            $blockedQueryTypes,
            $this->plugin->getBlockedQueryTypes()
        );
    }

    public function testPreExecuteRequestWithForcedEndpoint(): void
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

    public function testDefaultEndpointRestore(): void
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

    public function testBlockedQueryTypeNotLoadbalanced(): void
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

    public function testLoadbalancerRandomizing(): void
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

    public function testFailoverOnEndpointFailure(): void
    {
        $this->plugin = new TestLoadbalancer(); // special loadbalancer that returns endpoints in fixed order
        $this->client->setAdapter(new TestAdapterForFailover(true)); // set special mock that fails once with an HTTP exception
        $this->plugin->initPlugin($this->client, []);

        $request = new Request();
        $endpoints = [
           'server1' => 1,
           'server2' => 1,
        ];
        $this->plugin->setEndpoints($endpoints);
        $this->plugin->setFailoverEnabled(true);

        $endpointFailureListenerCalled = 0;
        /** @var EventDispatcher $eventDispatcher */
        $eventDispatcher = $this->client->getEventDispatcher();
        $eventDispatcher->addListener(
            LoadbalancerEvents::ENDPOINT_FAILURE,
            function (EndpointFailureEvent $event) use (&$endpointFailureListenerCalled): void {
                ++$endpointFailureListenerCalled;
                $this->assertSame('server1', $event->getEndpoint()->getKey());
                $this->assertSame('failover exception', $event->getException()->getStatusMessage());
            }
        );

        $query = new SelectQuery();
        $event = new PreCreateRequestEvent($query);
        $this->plugin->preCreateRequest($event);

        $event = new PreExecuteRequestEvent($request, new Endpoint());
        $this->plugin->preExecuteRequest($event);

        $this->assertSame(1, $endpointFailureListenerCalled);
        $this->assertSame('server2', $this->plugin->getLastEndpoint());
    }

    public function testFailoverOnStatusCodeFailure(): void
    {
        $this->plugin = new TestLoadbalancer(); // special loadbalancer that returns endpoints in fixed order
        $this->client->setAdapter(new TestAdapterForFailover(false)); // set special mock that fails once with an HTTP status error
        $this->plugin->initPlugin($this->client, []);

        $request = new Request();
        $endpoints = [
           'server1' => 1,
           'server2' => 1,
        ];
        $this->plugin->setEndpoints($endpoints);
        $this->plugin->setFailoverEnabled(true);
        $this->plugin->addFailoverStatusCode(504);

        $statusCodeFailureListenerCalled = 0;
        /** @var EventDispatcher $eventDispatcher */
        $eventDispatcher = $this->client->getEventDispatcher();
        $eventDispatcher->addListener(
            LoadbalancerEvents::STATUS_CODE_FAILURE,
            function (StatusCodeFailureEvent $event) use (&$statusCodeFailureListenerCalled): void {
                ++$statusCodeFailureListenerCalled;
                $this->assertSame('server1', $event->getEndpoint()->getKey());
                $this->assertSame(504, $event->getResponse()->getStatusCode());
            }
        );

        $query = new SelectQuery();
        $event = new PreCreateRequestEvent($query);
        $this->plugin->preCreateRequest($event);

        $event = new PreExecuteRequestEvent($request, new Endpoint());
        $this->plugin->preExecuteRequest($event);

        $this->assertSame(1, $statusCodeFailureListenerCalled);
        $this->assertSame('server2', $this->plugin->getLastEndpoint());
    }

    public function testFailoverMaxRetries(): void
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

        $this->expectException(RuntimeException::class);

        $event = new PreExecuteRequestEvent($request, new Endpoint());
        $this->plugin->preExecuteRequest($event);
    }
}

class TestLoadbalancer extends Loadbalancer
{
    protected int $counter = 0;

    /**
     * Get options array for a randomized endpoint.
     *
     * @return Endpoint
     */
    protected function getRandomEndpoint(): Endpoint
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
    protected bool $endpointFailure;

    protected int $counter = 0;

    protected int $failCount = 1;

    /**
     * Constructor.
     *
     * @param bool $endpointFailure Fail with an endpoint failure (true) or an HTTP status error (false)?
     */
    public function __construct(bool $endpointFailure = true)
    {
        $this->endpointFailure = $endpointFailure;
    }

    public function setFailCount(int $count): self
    {
        $this->failCount = $count;

        return $this;
    }

    public function execute(Request $request, Endpoint $endpoint): Response
    {
        ++$this->counter;

        if ($this->counter <= $this->failCount) {
            if ($this->endpointFailure) {
                throw new HttpException('failover exception');
            } else {
                return new Response('dummyvalue', ['HTTP/1.1 504 Gateway Timeout']);
            }
        }

        return new Response('dummyvalue', ['HTTP/1.1 200 OK']);
    }
}
