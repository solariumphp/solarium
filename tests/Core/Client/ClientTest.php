<?php

namespace Solarium\Tests\Core\Client;

use PHPUnit\Framework\TestCase;
use Solarium\Core\Client\Adapter\Http;
use Solarium\Core\Client\Client;
use Solarium\Core\Client\Endpoint;
use Solarium\Core\Client\Request;
use Solarium\Core\Client\Response;
use Solarium\Core\Event\Events;
use Solarium\Core\Event\PostCreateQuery as PostCreateQueryEvent;
use Solarium\Core\Event\PostCreateRequest as PostCreateRequestEvent;
use Solarium\Core\Event\PostCreateResult as PostCreateResultEvent;
use Solarium\Core\Event\PostExecute as PostExecuteEvent;
use Solarium\Core\Event\PostExecuteRequest as PostExecuteRequestEvent;
use Solarium\Core\Event\PreCreateQuery as PreCreateQueryEvent;
use Solarium\Core\Event\PreCreateRequest as PreCreateRequestEvent;
use Solarium\Core\Event\PreCreateResult as PreCreateResultEvent;
use Solarium\Core\Event\PreExecute as PreExecuteEvent;
use Solarium\Core\Event\PreExecuteRequest as PreExecuteRequestEvent;
use Solarium\Core\Plugin\AbstractPlugin;
use Solarium\Core\Query\AbstractRequestBuilder;
use Solarium\Core\Query\Result\Result;
use Solarium\Exception\InvalidArgumentException;
use Solarium\Exception\OutOfBoundsException;
use Solarium\Exception\UnexpectedValueException;
use Solarium\Plugin\Loadbalancer\Loadbalancer;
use Solarium\QueryType\Analysis\Query\Field as AnalysisQueryField;
use Solarium\QueryType\Extract\Query as ExtractQuery;
use Solarium\QueryType\Graph\Query as GraphQuery;
use Solarium\QueryType\Luke\Query as LukeQuery;
use Solarium\QueryType\ManagedResources\Query\Resources as ManagedResourcesQuery;
use Solarium\QueryType\ManagedResources\Query\Stopwords as ManagedStopwordsQuery;
use Solarium\QueryType\ManagedResources\Query\Synonyms as ManagedSynonymsQuery;
use Solarium\QueryType\MoreLikeThis\Query as MoreLikeThisQuery;
use Solarium\QueryType\Ping\Query as PingQuery;
use Solarium\QueryType\RealtimeGet\Query as RealtimeGetQuery;
use Solarium\QueryType\Select\Query\Query as SelectQuery;
use Solarium\QueryType\Server\Api\Query as ApiQuery;
use Solarium\QueryType\Server\Collections\Query\Query as CollectionsQuery;
use Solarium\QueryType\Server\Configsets\Query\Query as ConfigsetsQuery;
use Solarium\QueryType\Server\CoreAdmin\Query\Query as CoreAdminQuery;
use Solarium\QueryType\Spellcheck\Query as SpellcheckQuery;
use Solarium\QueryType\Stream\Query as StreamQuery;
use Solarium\QueryType\Suggester\Query as SuggesterQuery;
use Solarium\QueryType\Terms\Query as TermsQuery;
use Solarium\QueryType\Update\Query\Query as UpdateQuery;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

/*
 * @coversDefaultClass \Solarium\Core\Client\Client
 */
class ClientTest extends TestCase
{
    /**
     * @var Client
     */
    protected $client;

    public function setUp(): void
    {
        $this->client = new Client(new MyAdapter(), new EventDispatcher());
    }

    public function testConfigMode()
    {
        $options = [
            'endpoint' => [
                'myhost' => [
                    'host' => 'myhost',
                    'port' => 8080,
                ],
            ],
            'querytype' => [
                'myquerytype' => 'MyQuery',
            ],
            'plugin' => [
                'myplugin' => [
                    'plugin' => MyClientPlugin::class,
                    'options' => [
                        'option1' => 'value1',
                        'option2' => 'value2',
                    ],
                ],
            ],
        ];

        $this->client->setOptions($options);

        $adapter = $this->client->getAdapter();

        $this->assertThat($adapter, $this->isInstanceOf(MyAdapter::class));
        $this->assertSame(8080, $this->client->getEndpoint('myhost')->getPort());

        $queryTypes = $this->client->getQueryTypes();
        $this->assertSame(
            $options['querytype']['myquerytype'],
            $queryTypes['myquerytype']
        );

        $plugin = $this->client->getPlugin('myplugin');
        $this->assertThat($plugin, $this->isInstanceOf(MyClientPlugin::class));
        $this->assertSame($options['plugin']['myplugin']['options'], $plugin->getOptions());
    }

    public function testGetEventDispatcher()
    {
        $this->assertInstanceOf(EventDispatcherInterface::class, $this->client->getEventDispatcher());
        $eventDispatcher = $this->createMock(EventDispatcherInterface::class);
        $this->client->setEventDispatcher($eventDispatcher);

        $this->assertSame($eventDispatcher, $this->client->getEventDispatcher());
    }

    public function testEventDispatcherInjection()
    {
        $eventDispatcher = $this->createMock(EventDispatcherInterface::class);
        $client = new Client(new MyAdapter(), $eventDispatcher);
        $this->assertSame($eventDispatcher, $client->getEventDispatcher());
    }

    public function testConfigModeWithoutKeys()
    {
        $options = [
            'endpoint' => [
                [
                    'key' => 'myhost',
                    'host' => 'myhost',
                    'port' => 8080,
                ],
            ],
            'querytype' => [
                [
                    'type' => 'myquerytype',
                    'query' => 'MyQuery',
                ],
            ],
            'plugin' => [
                 [
                    'key' => 'myplugin',
                    'plugin' => MyClientPlugin::class,
                    'options' => [
                        'option1' => 'value1',
                        'option2' => 'value2',
                    ],
                ],
            ],
        ];

        $this->client->setOptions($options);

        $adapter = $this->client->getAdapter();

        $this->assertThat($adapter, $this->isInstanceOf(MyAdapter::class));
        $this->assertSame(8080, $this->client->getEndpoint('myhost')->getPort());

        $queryTypes = $this->client->getQueryTypes();
        $this->assertSame(
            'MyQuery',
            $queryTypes['myquerytype']
        );

        $plugin = $this->client->getPlugin('myplugin');
        $this->assertThat($plugin, $this->isInstanceOf(MyClientPlugin::class));
        $this->assertSame($options['plugin'][0]['options'], $plugin->getOptions());
    }

    public function testCreateEndpoint()
    {
        $endpoint = $this->client->createEndpoint();
        $this->assertNull($endpoint->getKey());
        $this->assertThat($endpoint, $this->isInstanceOf(Endpoint::class));
    }

    public function testCreateEndpointWithKey()
    {
        $endpoint = $this->client->createEndpoint('key1');
        $this->assertSame('key1', $endpoint->getKey());
        $this->assertThat($endpoint, $this->isInstanceOf(Endpoint::class));
    }

    public function testCreateEndpointWithSetAsDefault()
    {
        $this->client->createEndpoint('key3', true);
        $endpoint = $this->client->getEndpoint();
        $this->assertSame('key3', $endpoint->getKey());
    }

    public function testCreateEndpointWithArray()
    {
        $options = [
            'key' => 'server2',
            'host' => 's2.local',
        ];

        $endpoint = $this->client->createEndpoint($options);
        $this->assertSame('server2', $endpoint->getKey());
        $this->assertSame('s2.local', $endpoint->getHost());
        $this->assertThat($endpoint, $this->isInstanceOf(Endpoint::class));
    }

    public function testAddAndGetEndpoint()
    {
        $endpoint = $this->client->createEndpoint();
        $endpoint->setKey('s3');
        $endpoint->setHost('s3.local');
        $this->client->clearEndpoints();
        $this->client->addEndpoint($endpoint);

        $this->assertSame(
            ['s3' => $endpoint],
            $this->client->getEndpoints()
        );

        // check default endpoint
        $this->assertSame(
            $endpoint,
            $this->client->getEndpoint()
        );
    }

    public function testAddEndpointWithArray()
    {
        $options = [
            'key' => 'server2',
            'host' => 's2.local',
        ];

        $endpoint = $this->client->createEndpoint($options);
        $this->client->addEndpoint($endpoint);

        $this->assertSame(
            $endpoint,
            $this->client->getEndpoint('server2')
        );
    }

    public function testAddEndpointWithoutKey()
    {
        $endpoint = $this->client->createEndpoint();

        $this->expectException(InvalidArgumentException::class);
        $this->client->addEndpoint($endpoint);
    }

    public function testAddEndpointWithEmptyKey()
    {
        $this->expectException(InvalidArgumentException::class);
        $this->client->createEndpoint('');
    }

    public function testAddEndpointWithDuplicateKey()
    {
        $this->client->createEndpoint('s1');
        $this->expectException(InvalidArgumentException::class);
        $this->client->createEndpoint('s1');
    }

    public function testAddAndGetEndpoints()
    {
        $options = [
            // use array key
            's1' => ['host' => 's1.local'],

            // use key array entry
            ['key' => 's2', 'host' => 's2.local'],
        ];

        $this->client->addEndpoints($options);
        $endpoints = $this->client->getEndpoints();

        $this->assertSame('s1.local', $endpoints['s1']->getHost());
        $this->assertSame('s2.local', $endpoints['s2']->getHost());
    }

    public function testGetEndpointWithInvalidKey()
    {
        $this->client->createEndpoint('s1');

        $this->expectException(OutOfBoundsException::class);
        $this->client->getEndpoint('s2');
    }

    public function testSetAndGetEndpoints()
    {
        $endpoint1 = $this->client->createEndpoint('s1');
        $this->client->addEndpoint($endpoint1);

        $endpoint2 = $this->client->createEndpoint('s2');
        $endpoint3 = $this->client->createEndpoint('s3');
        $this->client->setEndpoints([$endpoint2, $endpoint3]);

        $this->assertSame(
            ['s2' => $endpoint2, 's3' => $endpoint3],
            $this->client->getEndpoints()
        );
    }

    public function testRemoveEndpointWithKey()
    {
        $endpoint1 = $this->client->createEndpoint('s1');
        $endpoint2 = $this->client->createEndpoint('s2');
        $endpoint3 = $this->client->createEndpoint('s3');
        $this->client->setEndpoints([$endpoint1, $endpoint2, $endpoint3]);
        $this->client->removeEndpoint('s1');

        $this->assertSame(
            ['s2' => $endpoint2, 's3' => $endpoint3],
            $this->client->getEndpoints()
        );
    }

    public function testRemoveEndpointWithObject()
    {
        $endpoint1 = $this->client->createEndpoint('s1');
        $endpoint2 = $this->client->createEndpoint('s2');
        $endpoint3 = $this->client->createEndpoint('s3');
        $this->client->setEndpoints([$endpoint1, $endpoint2, $endpoint3]);
        $this->client->removeEndpoint($endpoint1);

        $this->assertSame(
            ['s2' => $endpoint2, 's3' => $endpoint3],
            $this->client->getEndpoints()
        );
    }

    public function testClearEndpoints()
    {
        $endpoint1 = $this->client->createEndpoint('s1');
        $endpoint2 = $this->client->createEndpoint('s2');

        $this->client->setEndpoints([$endpoint1, $endpoint2]);
        $this->client->clearEndpoints();

        $this->assertSame(
            [],
            $this->client->getEndpoints()
        );
    }

    public function testSetDefaultEndpointWithKey()
    {
        $endpoint1 = $this->client->createEndpoint('s1');
        $endpoint2 = $this->client->createEndpoint('s2');

        $this->client->setEndpoints([$endpoint1, $endpoint2]);

        $this->assertSame($endpoint1, $this->client->getEndpoint());
        $this->client->setDefaultEndpoint('s2');
        $this->assertSame($endpoint2, $this->client->getEndpoint());
    }

    public function testSetDefaultEndpointWithObject()
    {
        $endpoint1 = $this->client->createEndpoint('s1');
        $endpoint2 = $this->client->createEndpoint('s2');

        $this->client->setEndpoints([$endpoint1, $endpoint2]);

        $this->assertSame($endpoint1, $this->client->getEndpoint());
        $this->client->setDefaultEndpoint($endpoint2);
        $this->assertSame($endpoint2, $this->client->getEndpoint());
    }

    public function testSetDefaultEndpointWithInvalidKey()
    {
        $this->expectException(OutOfBoundsException::class);
        $this->client->setDefaultEndpoint('invalidkey');
    }

    public function testSetAndGetAdapterWithObject()
    {
        $adapterClass = MyAdapter::class;
        $this->client->setAdapter(new $adapterClass());
        $this->assertThat($this->client->getAdapter(), $this->isInstanceOf($adapterClass));
    }

    public function testRegisterQueryTypeAndGetQueryTypes()
    {
        $queryTypes = $this->client->getQueryTypes();

        $this->client->registerQueryType('myquerytype', 'myquery');

        $queryTypes['myquerytype'] = 'myquery';

        $this->assertSame(
            $queryTypes,
            $this->client->getQueryTypes()
        );
    }

    public function testRegisterAndGetPlugin()
    {
        $options = ['option1' => 1];
        $this->client->registerPlugin('testplugin', MyClientPlugin::class, $options);

        $plugin = $this->client->getPlugin('testplugin');

        $this->assertThat(
            $plugin,
            $this->isInstanceOf(MyClientPlugin::class)
        );

        $this->assertSame(
            $options,
            $plugin->getOptions()
        );
    }

    public function testRegisterInvalidPlugin()
    {
        $this->expectException(InvalidArgumentException::class);
        $this->client->registerPlugin('testplugin', 'StdClass');
    }

    public function testGetInvalidPlugin()
    {
        $this->assertNull(
            $this->client->getPlugin('invalidplugin', false)
        );
    }

    public function testAutoloadPlugin()
    {
        $loadbalancer = $this->client->getPlugin('loadbalancer');
        $this->assertThat(
            $loadbalancer,
            $this->isInstanceOf(Loadbalancer::class)
        );
    }

    public function testAutoloadInvalidPlugin()
    {
        $this->expectException(OutOfBoundsException::class);
        $this->client->getPlugin('invalidpluginname');
    }

    public function testRemoveAndGetPlugins()
    {
        $options = ['option1' => 1];
        $this->client->registerPlugin('testplugin', MyClientPlugin::class, $options);

        $plugin = $this->client->getPlugin('testplugin');
        $plugins = $this->client->getPlugins();

        $this->assertSame(
            ['testplugin' => $plugin],
            $plugins
        );

        $this->client->removePlugin('testplugin');
        $plugins = $this->client->getPlugins();

        $this->assertSame(
            [],
            $plugins
        );
    }

    public function testDeinitPluginAndGetPluginsWithObjectInput()
    {
        $options = ['option1' => 1];
        $this->client->registerPlugin('testplugin', MyClientPlugin::class, $options);

        $plugin = $this->client->getPlugin('testplugin');
        $plugins = $this->client->getPlugins();

        $this->assertSame(
            ['testplugin' => $plugin],
            $plugins
        );

        $this->client->removePlugin($plugin);
        $plugins = $this->client->getPlugins();

        $this->assertSame(
            [],
            $plugins
        );
    }

    public function testCreateRequest()
    {
        $queryStub = $this->createMock(SelectQuery::class);

        $observer = $this->createMock(AbstractRequestBuilder::class);
        $observer->expects($this->once())
                 ->method('build')
                 ->with($this->equalTo($queryStub))
                 ->willReturn(new Request());

        $queryStub->expects($this->any())
             ->method('getType')
             ->willReturn('testquerytype');
        $queryStub->expects($this->any())
             ->method('getRequestBuilder')
             ->willReturn($observer);

        $this->client->registerQueryType('testquerytype', SelectQuery::class);
        $this->client->createRequest($queryStub);
    }

    public function testCreateRequestInvalidQueryType()
    {
        $queryStub = $this->createMock(\stdClass::class);

        $this->expectException(\TypeError::class);
        $this->client->createRequest($queryStub);
    }

    public function testCreateRequestPrePlugin()
    {
        $query = new SelectQuery();
        $expectedEvent = new PreCreateRequestEvent($query);
        if (method_exists($expectedEvent, 'setDispatcher')) {
            $expectedEvent->setDispatcher($this->client->getEventDispatcher());
            $expectedEvent->setName(Events::PRE_CREATE_REQUEST);
        }

        $observer = $this->getMockBuilder(AbstractPlugin::class)
            ->addMethods(['preCreateRequest'])
            ->getMock();
        /* @phpstan-ignore-next-line */
        $observer->expects($this->once())
                 ->method('preCreateRequest')
                 ->with($this->equalTo($expectedEvent));

        $this->client->registerPlugin('testplugin', $observer);
        $this->client->getEventDispatcher()->addListener(
            Events::PRE_CREATE_REQUEST,
            [$observer, 'preCreateRequest']
        );

        $this->client->createRequest($query);
    }

    public function testCreateRequestPostPlugin()
    {
        $query = new SelectQuery();
        $request = $this->client->createRequest($query);
        $expectedEvent = new PostCreateRequestEvent($query, $request);
        if (method_exists($expectedEvent, 'setDispatcher')) {
            $expectedEvent->setDispatcher($this->client->getEventDispatcher());
            $expectedEvent->setName(Events::POST_CREATE_REQUEST);
        }

        $observer = $this->getMockBuilder(AbstractPlugin::class)
            ->addMethods(['postCreateRequest'])
            ->getMock();
        /* @phpstan-ignore-next-line */
        $observer->expects($this->once())
                 ->method('postCreateRequest')
                 ->with($this->equalTo($expectedEvent));

        $this->client->registerPlugin('testplugin', $observer);
        $this->client->getEventDispatcher()->addListener(
            Events::POST_CREATE_REQUEST,
            [$observer, 'postCreateRequest']
        );

        $this->client->createRequest($query);
    }

    public function testCreateRequestWithOverridingPlugin()
    {
        $expectedRequest = new Request();
        $expectedRequest->setHandler('something-unique-345978');

        $query = new SelectQuery();
        $expectedEvent = new PreCreateRequestEvent($query);
        if (method_exists($expectedEvent, 'setDispatcher')) {
            $expectedEvent->setDispatcher($this->client->getEventDispatcher());
            $expectedEvent->setName(Events::PRE_CREATE_REQUEST);
        }

        $test = $this;
        $this->client->getEventDispatcher()->addListener(
            Events::PRE_CREATE_REQUEST,
            function (PreCreateRequestEvent $event) use ($test, $expectedRequest, $expectedEvent) {
                $test->assertEquals($expectedEvent, $event);
                $event->setRequest($expectedRequest);
            }
        );

        $returnedRequest = $this->client->createRequest($query);

        $this->assertSame(
            $expectedRequest,
            $returnedRequest
        );
    }

    public function testCreateResult()
    {
        $query = new SelectQuery();
        $response = new Response('', ['HTTP 1.0 200 OK']);
        $result = $this->client->createResult($query, $response);

        $this->assertThat(
            $result,
            $this->isInstanceOf($query->getResultClass())
        );
    }

    public function testCreateResultPrePlugin()
    {
        $query = new SelectQuery();
        $response = new Response('', ['HTTP 1.0 200 OK']);
        $expectedEvent = new PreCreateResultEvent($query, $response);
        if (method_exists($expectedEvent, 'setDispatcher')) {
            $expectedEvent->setDispatcher($this->client->getEventDispatcher());
            $expectedEvent->setName(Events::PRE_CREATE_RESULT);
        }

        $observer = $this->getMockBuilder(AbstractPlugin::class)
            ->addMethods(['preCreateResult'])
            ->getMock();
        /* @phpstan-ignore-next-line */
        $observer->expects($this->once())
                 ->method('preCreateResult')
                 ->with($this->equalTo($expectedEvent));

        $this->client->registerPlugin('testplugin', $observer);
        $this->client->getEventDispatcher()->addListener(
            Events::PRE_CREATE_RESULT,
            [$observer, 'preCreateResult']
        );

        $this->client->createResult($query, $response);
    }

    public function testCreateResultPostPlugin()
    {
        $query = new SelectQuery();
        $response = new Response('', ['HTTP 1.0 200 OK']);
        $result = $this->client->createResult($query, $response);
        $expectedEvent = new PostCreateResultEvent($query, $response, $result);
        if (method_exists($expectedEvent, 'setDispatcher')) {
            $expectedEvent->setDispatcher($this->client->getEventDispatcher());
            $expectedEvent->setName(Events::POST_CREATE_RESULT);
        }
        $observer = $this->getMockBuilder(AbstractPlugin::class)
            ->addMethods(['postCreateResult'])
            ->getMock();
        /* @phpstan-ignore-next-line */
        $observer->expects($this->once())
                 ->method('postCreateResult')
                 ->with($this->equalTo($expectedEvent));

        $this->client->registerPlugin('testplugin', $observer);
        $this->client->getEventDispatcher()->addListener(
            Events::POST_CREATE_RESULT,
            [$observer, 'postCreateResult']
        );

        $this->client->createResult($query, $response);
    }

    public function testCreateResultWithOverridingPlugin()
    {
        $query = new SelectQuery();
        $response = new Response('test 1234', ['HTTP 1.0 200 OK']);
        $expectedEvent = new PreCreateResultEvent($query, $response);
        if (method_exists($expectedEvent, 'setDispatcher')) {
            $expectedEvent->setDispatcher($this->client->getEventDispatcher());
            $expectedEvent->setName(Events::PRE_CREATE_RESULT);
        }
        $expectedResult = new Result($query, $response);

        $test = $this;
        $this->client->getEventDispatcher()->addListener(
            Events::PRE_CREATE_RESULT,
            function (PreCreateResultEvent $event) use ($test, $expectedResult, $expectedEvent) {
                $test->assertEquals($expectedEvent, $event);
                $event->setResult($expectedResult);
            }
        );

        $returnedResult = $this->client->createResult($query, $response);

        $this->assertSame(
            $expectedResult,
            $returnedResult
        );
    }

    public function testCreateResultWithInvalidResultClass()
    {
        $query = new SelectQuery();
        $query->setResultClass(\stdClass::class);
        $response = new Response('', ['HTTP 1.0 200 OK']);

        $this->expectException(UnexpectedValueException::class);
        $this->expectExceptionMessage('Result class must implement the ResultInterface');
        $this->client->createResult($query, $response);
    }

    public function testExecute()
    {
        $query = new PingQuery();
        $request = new Request();
        $response = new Response('', ['HTTP 1.0 200 OK']);
        $result = new Result($query, $response);

        $observer = $this->getMockBuilder(Client::class)
            ->onlyMethods(['createRequest', 'executeRequest', 'createResult'])
            ->setConstructorArgs([new MyAdapter(), new EventDispatcher()])
            ->getMock();

        $observer->expects($this->once())
                 ->method('createRequest')
                 ->with($this->equalTo($query))
                 ->willReturn($request);

        $observer->expects($this->once())
                 ->method('executeRequest')
                 ->with($this->equalTo($request))
                 ->willReturn($response);

        $observer->expects($this->once())
                 ->method('createResult')
                 ->with($this->equalTo($query), $this->equalTo($response))
                 ->willReturn($result);

        $observer->execute($query);
    }

    public function testExecutePrePlugin()
    {
        $query = new PingQuery();
        $response = new Response('', ['HTTP 1.0 200 OK']);
        $result = new Result($query, $response);
        $expectedEvent = new PreExecuteEvent($query);

        $mock = $this->getMockBuilder(Client::class)
            ->onlyMethods(['createRequest', 'executeRequest', 'createResult'])
            ->setConstructorArgs([new MyAdapter(), new EventDispatcher()])
            ->getMock();

        $mock->expects($this->once())
             ->method('createRequest')
             ->willReturn(new Request());

        $mock->expects($this->once())
             ->method('executeRequest')
             ->willReturn(new Response('dummyresponse'));

        $mock->expects($this->once())
             ->method('createResult')
             ->willReturn($result);

        $observer = $this->getMockBuilder(AbstractPlugin::class)
            ->addMethods(['preExecute'])
            ->getMock();
        /* @phpstan-ignore-next-line */
        $observer->expects($this->once())
                 ->method('preExecute')
                 ->with($this->equalTo($expectedEvent));

        $mock->getEventDispatcher()->addListener(Events::PRE_EXECUTE, [$observer, 'preExecute']);

        if (method_exists($expectedEvent, 'setDispatcher')) {
            $expectedEvent->setName(Events::PRE_EXECUTE);
            $expectedEvent->setDispatcher($mock->getEventDispatcher());
        }

        $mock->execute($query);
    }

    public function testExecutePostPlugin()
    {
        $query = new PingQuery();
        $response = new Response('', ['HTTP 1.0 200 OK']);
        $result = new Result($query, $response);
        $expectedEvent = new PostExecuteEvent($query, $result);

        $mock = $this->getMockBuilder(Client::class)
            ->onlyMethods(['createRequest', 'executeRequest', 'createResult'])
            ->setConstructorArgs([new MyAdapter(), new EventDispatcher()])
            ->getMock();

        $mock->expects($this->once())
             ->method('createRequest')
             ->willReturn(new Request());

        $mock->expects($this->once())
             ->method('executeRequest')
             ->willReturn(new Response('dummyresponse'));

        $mock->expects($this->once())
             ->method('createResult')
             ->willReturn($result);

        $observer = $this->getMockBuilder(AbstractPlugin::class)
            ->addMethods(['postExecute'])
            ->getMock();
        /* @phpstan-ignore-next-line */
        $observer->expects($this->once())
                 ->method('postExecute')
                 ->with($this->equalTo($expectedEvent));

        $mock->getEventDispatcher()->addListener(Events::POST_EXECUTE, [$observer, 'postExecute']);

        if (method_exists($expectedEvent, 'setDispatcher')) {
            $expectedEvent->setName(Events::POST_EXECUTE);
            $expectedEvent->setDispatcher($mock->getEventDispatcher());
        }

        $mock->execute($query);
    }

    public function testExecuteWithOverridingPlugin()
    {
        $query = new PingQuery();
        $response = new Response('', ['HTTP 1.0 200 OK']);
        $expectedResult = new Result($query, $response);
        $expectedEvent = new PreExecuteEvent($query);
        if (method_exists($expectedEvent, 'setDispatcher')) {
            $expectedEvent->setDispatcher($this->client->getEventDispatcher());
            $expectedEvent->setName(Events::PRE_EXECUTE);
        }

        $test = $this;
        $this->client->getEventDispatcher()->addListener(
            Events::PRE_EXECUTE,
            function (PreExecuteEvent $event) use ($test, $expectedResult, $expectedEvent) {
                $test->assertEquals($expectedEvent, $event);
                $event->setResult($expectedResult);
            }
        );

        $returnedResult = $this->client->execute($query);

        $this->assertSame(
            $expectedResult,
            $returnedResult
        );
    }

    public function testExecuteRequest()
    {
        $request = new Request();
        $response = new Response('', ['HTTP 1.0 200 OK']);

        $observer = $this->getMockBuilder(Http::class)
            ->onlyMethods(['execute'])
            ->getMock();
        $observer->expects($this->once())
                 ->method('execute')
                 ->with($this->equalTo($request))
                 ->willReturn($response);

        $this->client->setAdapter($observer);
        $returnedResponse = $this->client->executeRequest($request);

        $this->assertSame(
            $response,
            $returnedResponse
        );
    }

    public function testExecuteRequestPrePlugin()
    {
        $request = new Request();
        $endpoint = $this->client->createEndpoint('s1');
        $response = new Response('', ['HTTP 1.0 200 OK']);
        $expectedEvent = new PreExecuteRequestEvent($request, $endpoint);
        if (method_exists($expectedEvent, 'setDispatcher')) {
            $expectedEvent->setDispatcher($this->client->getEventDispatcher());
            $expectedEvent->setName(Events::PRE_EXECUTE_REQUEST);
        }

        $mockAdapter = $this->getMockBuilder(Http::class)
            ->onlyMethods(['execute'])
            ->getMock();
        $mockAdapter->expects($this->once())
                 ->method('execute')
                 ->with($this->equalTo($request))
                 ->willReturn($response);
        $this->client->setAdapter($mockAdapter);

        $observer = $this->getMockBuilder(AbstractPlugin::class)
            ->addMethods(['preExecuteRequest'])
            ->getMock();
        /* @phpstan-ignore-next-line */
        $observer->expects($this->once())
                 ->method('preExecuteRequest')
                 ->with($this->equalTo($expectedEvent));

        $this->client->getEventDispatcher()->addListener(
            Events::PRE_EXECUTE_REQUEST,
            [$observer, 'preExecuteRequest']
        );
        $this->client->executeRequest($request, $endpoint);
    }

    public function testExecuteRequestPostPlugin()
    {
        $request = new Request();
        $endpoint = $this->client->createEndpoint('s1');
        $response = new Response('', ['HTTP 1.0 200 OK']);
        $expectedEvent = new PostExecuteRequestEvent($request, $endpoint, $response);
        if (method_exists($expectedEvent, 'setDispatcher')) {
            $expectedEvent->setDispatcher($this->client->getEventDispatcher());
            $expectedEvent->setName(Events::POST_EXECUTE_REQUEST);
        }

        $mockAdapter = $this->getMockBuilder(Http::class)
            ->onlyMethods(['execute'])
            ->getMock();
        $mockAdapter->expects($this->any())
                 ->method('execute')
                 ->with($this->equalTo($request))
                 ->willReturn($response);
        $this->client->setAdapter($mockAdapter);

        $observer = $this->getMockBuilder(AbstractPlugin::class)
            ->addMethods(['postExecuteRequest'])
            ->getMock();
        /* @phpstan-ignore-next-line */
        $observer->expects($this->once())
                 ->method('postExecuteRequest')
                 ->with($this->equalTo($expectedEvent));

        $this->client->getEventDispatcher()->addListener(
            Events::POST_EXECUTE_REQUEST,
            [$observer, 'postExecuteRequest']
        );
        $this->client->executeRequest($request, $endpoint);
    }

    public function testExecuteRequestWithOverridingPlugin()
    {
        $request = new Request();
        $response = new Response('', ['HTTP 1.0 200 OK']);
        $endpoint = $this->client->createEndpoint('s1');
        $expectedEvent = new PreExecuteRequestEvent($request, $endpoint);
        if (method_exists($expectedEvent, 'setDispatcher')) {
            $expectedEvent->setDispatcher($this->client->getEventDispatcher());
            $expectedEvent->setName(Events::PRE_EXECUTE_REQUEST);
        }

        $test = $this;
        $this->client->getEventDispatcher()->addListener(
            Events::PRE_EXECUTE_REQUEST,
            function (PreExecuteRequestEvent $event) use ($test, $response, $expectedEvent) {
                $test->assertEquals($expectedEvent, $event);
                $event->setResponse($response);
            }
        );

        $returnedResponse = $this->client->executeRequest($request, $endpoint);

        $this->assertSame(
            $response,
            $returnedResponse
        );
    }

    public function testPing()
    {
        $query = new PingQuery();

        $observer = $this->getMockBuilder(Client::class)
            ->onlyMethods(['execute'])
            ->setConstructorArgs([new MyAdapter(), new EventDispatcher()])
            ->getMock();
        $observer->expects($this->once())
                 ->method('execute')
                 ->with($this->equalTo($query))
                 ->willReturn(new \Solarium\QueryType\Ping\Result($query, new Response('dummyresponse', ['HTTP 1.0 200 OK'])));

        $observer->ping($query);
    }

    public function testSelect()
    {
        $query = new SelectQuery();

        $observer = $this->getMockBuilder(Client::class)
            ->onlyMethods(['execute'])
            ->setConstructorArgs([new MyAdapter(), new EventDispatcher()])
            ->getMock();
        $observer->expects($this->once())
                 ->method('execute')
                 ->with($this->equalTo($query))
                 ->willReturn(new \Solarium\QueryType\Select\Result\Result($query, new Response('dummyresponse', ['HTTP 1.0 200 OK'])));

        $observer->select($query);
    }

    public function testUpdate()
    {
        $query = new UpdateQuery();

        $observer = $this->getMockBuilder(Client::class)
            ->onlyMethods(['execute'])
            ->setConstructorArgs([new MyAdapter(), new EventDispatcher()])
            ->getMock();
        $observer->expects($this->once())
                 ->method('execute')
                 ->with($this->equalTo($query))
                 ->willReturn(new \Solarium\QueryType\Update\Result($query, new Response('dummyresponse', ['HTTP 1.0 200 OK'])));

        $observer->update($query);
    }

    public function testMoreLikeThis()
    {
        $query = new MoreLikeThisQuery();

        $observer = $this->getMockBuilder(Client::class)
            ->onlyMethods(['execute'])
            ->setConstructorArgs([new MyAdapter(), new EventDispatcher()])
            ->getMock();
        $observer->expects($this->once())
                 ->method('execute')
                 ->with($this->equalTo($query))
                 ->willReturn(new \Solarium\QueryType\MoreLikeThis\Result($query, new Response('dummyresponse', ['HTTP 1.0 200 OK'])));

        $observer->moreLikeThis($query);
    }

    public function testAnalyze()
    {
        $query = new AnalysisQueryField();

        $observer = $this->getMockBuilder(Client::class)
            ->onlyMethods(['execute'])
            ->setConstructorArgs([new MyAdapter(), new EventDispatcher()])
            ->getMock();
        $observer->expects($this->once())
                 ->method('execute')
                 ->with($this->equalTo($query))
                 ->willReturn(new Result($query, new Response('dummyresponse', ['HTTP 1.0 200 OK'])));

        $observer->analyze($query);
    }

    public function testTerms()
    {
        $query = new TermsQuery();

        $observer = $this->getMockBuilder(Client::class)
            ->onlyMethods(['execute'])
            ->setConstructorArgs([new MyAdapter(), new EventDispatcher()])
            ->getMock();
        $observer->expects($this->once())
                 ->method('execute')
                 ->with($this->equalTo($query))
            ->willReturn(new \Solarium\QueryType\Terms\Result($query, new Response('dummyresponse', ['HTTP 1.0 200 OK'])));

        $observer->terms($query);
    }

    public function testSpellcheck()
    {
        $query = new SpellcheckQuery();

        $observer = $this->getMockBuilder(Client::class)
            ->onlyMethods(['execute'])
            ->setConstructorArgs([new MyAdapter(), new EventDispatcher()])
            ->getMock();
        $observer->expects($this->once())
                 ->method('execute')
                 ->with($this->equalTo($query))
                 ->willReturn(new \Solarium\QueryType\Spellcheck\Result\Result($query, new Response('dummyresponse', ['HTTP 1.0 200 OK'])));

        $observer->spellcheck($query);
    }

    public function testSuggester()
    {
        $query = new SuggesterQuery();

        $observer = $this->getMockBuilder(Client::class)
            ->onlyMethods(['execute'])
            ->setConstructorArgs([new MyAdapter(), new EventDispatcher()])
            ->getMock();
        $observer->expects($this->once())
                 ->method('execute')
                 ->with($this->equalTo($query))
                 ->willReturn(new \Solarium\QueryType\Suggester\Result\Result($query, new Response('dummyresponse', ['HTTP 1.0 200 OK'])));

        $observer->suggester($query);
    }

    public function testExtract()
    {
        $query = new ExtractQuery();

        $observer = $this->getMockBuilder(Client::class)
            ->onlyMethods(['execute'])
            ->setConstructorArgs([new MyAdapter(), new EventDispatcher()])
            ->getMock();
        $observer->expects($this->once())
                 ->method('execute')
                 ->with($this->equalTo($query))
            ->willReturn(new \Solarium\QueryType\Extract\Result($query, new Response('dummyresponse', ['HTTP 1.0 200 OK'])));

        $observer->extract($query);
    }

    public function testRealtimeGet()
    {
        $query = new RealtimeGetQuery();

        $observer = $this->getMockBuilder(Client::class)
            ->onlyMethods(['execute'])
            ->setConstructorArgs([new MyAdapter(), new EventDispatcher()])
            ->getMock();
        $observer->expects($this->once())
                 ->method('execute')
                 ->with($this->equalTo($query))
            ->willReturn(new \Solarium\QueryType\RealtimeGet\Result($query, new Response('dummyresponse', ['HTTP 1.0 200 OK'])));

        $observer->realtimeGet($query);
    }

    public function testLuke()
    {
        $query = new LukeQuery();

        $observer = $this->getMockBuilder(Client::class)
            ->onlyMethods(['execute'])
            ->setConstructorArgs([new MyAdapter(), new EventDispatcher()])
            ->getMock();
        $observer->expects($this->once())
                 ->method('execute')
                 ->with($this->equalTo($query))
            ->willReturn(new \Solarium\QueryType\Luke\Result\Result($query, new Response('dummyresponse', ['HTTP 1.0 200 OK'])));

        $observer->luke($query);
    }

    public function testCoreAdmin()
    {
        $query = new CoreAdminQuery();

        $observer = $this->getMockBuilder(Client::class)
            ->onlyMethods(['execute'])
            ->setConstructorArgs([new MyAdapter(), new EventDispatcher()])
            ->getMock();
        $observer->expects($this->once())
                 ->method('execute')
                 ->with($this->equalTo($query))
            ->willReturn(new \Solarium\QueryType\Server\CoreAdmin\Result\Result($query, new Response('dummyresponse', ['HTTP 1.0 200 OK'])));

        $observer->coreAdmin($query);
    }

    public function testCollections()
    {
        $query = new CollectionsQuery();

        $observer = $this->getMockBuilder(Client::class)
            ->onlyMethods(['execute'])
            ->setConstructorArgs([new MyAdapter(), new EventDispatcher()])
            ->getMock();
        $observer->expects($this->once())
                 ->method('execute')
                 ->with($this->equalTo($query))
            ->willReturn(new \Solarium\QueryType\Server\Collections\Result\ClusterStatusResult($query, new Response('dummyresponse', ['HTTP 1.0 200 OK'])));

        $observer->collections($query);
    }

    public function testConfigsets()
    {
        $query = new ConfigsetsQuery();

        $observer = $this->getMockBuilder(Client::class)
            ->onlyMethods(['execute'])
            ->setConstructorArgs([new MyAdapter(), new EventDispatcher()])
            ->getMock();
        $observer->expects($this->once())
                 ->method('execute')
                 ->with($this->equalTo($query))
            ->willReturn(new \Solarium\QueryType\Server\Configsets\Result\ConfigsetsResult($query, new Response('dummyresponse', ['HTTP 1.0 200 OK'])));

        $observer->configsets($query);
    }

    public function testCreateQuery()
    {
        $options = ['optionA' => 1, 'optionB' => 2];
        $query = $this->client->createQuery(Client::QUERY_SELECT, $options);

        // check class mapping
        $this->assertThat($query, $this->isInstanceOf('Solarium\QueryType\Select\Query\Query'));

        // check option forwarding
        $queryOptions = $query->getOptions();
        $this->assertSame(
            $options['optionB'],
            $queryOptions['optionB']
        );
    }

    public function testCreateQueryWithInvalidQueryType()
    {
        $this->expectException(InvalidArgumentException::class);
        $this->client->createQuery('invalidtype');
    }

    public function testCreateQueryWithInvalidClass()
    {
        $this->client->registerQueryType('invalidquery', '\\StdClass');
        $this->expectException(UnexpectedValueException::class);
        $this->client->createQuery('invalidquery');
    }

    public function testCreateQueryPrePlugin()
    {
        $type = Client::QUERY_SELECT;
        $options = ['optionA' => 1, 'optionB' => 2];
        $expectedEvent = new PreCreateQueryEvent($type, $options);
        if (method_exists($expectedEvent, 'setDispatcher')) {
            $expectedEvent->setDispatcher($this->client->getEventDispatcher());
            $expectedEvent->setName(Events::PRE_CREATE_QUERY);
        }

        $observer = $this->getMockBuilder(AbstractPlugin::class)
            ->addMethods(['preCreateQuery'])
            ->getMock();
        /* @phpstan-ignore-next-line */
        $observer->expects($this->once())
                 ->method('preCreateQuery')
                 ->with($this->equalTo($expectedEvent));

        $this->client->getEventDispatcher()->addListener(Events::PRE_CREATE_QUERY, [$observer, 'preCreateQuery']);
        $this->client->createQuery($type, $options);
    }

    public function testCreateQueryWithOverridingPlugin()
    {
        $type = Client::QUERY_SELECT;
        $options = ['query' => 'test789'];
        $expectedQuery = new SelectQuery();
        $expectedQuery->setQuery('test789');
        $expectedEvent = new PreCreateQueryEvent($type, $options);
        if (method_exists($expectedEvent, 'setDispatcher')) {
            $expectedEvent->setDispatcher($this->client->getEventDispatcher());
            $expectedEvent->setName(Events::PRE_CREATE_QUERY);
        }

        $test = $this;
        $this->client->getEventDispatcher()->addListener(
            Events::PRE_CREATE_QUERY,
            function (PreCreateQueryEvent $event) use ($test, $expectedQuery, $expectedEvent) {
                $test->assertEquals($expectedEvent, $event);
                $event->setQuery($expectedQuery);
            }
        );

        $returnedQuery = $this->client->createQuery($type, $options);

        $this->assertSame(
            $expectedQuery,
            $returnedQuery
        );
    }

    public function testCreateQueryPostPlugin()
    {
        $type = Client::QUERY_SELECT;
        $options = ['optionA' => 1, 'optionB' => 2];
        $query = $this->client->createQuery($type, $options);
        $expectedEvent = new PostCreateQueryEvent($type, $options, $query);
        if (method_exists($expectedEvent, 'setDispatcher')) {
            $expectedEvent->setDispatcher($this->client->getEventDispatcher());
            $expectedEvent->setName(Events::POST_CREATE_QUERY);
        }

        $observer = $this->getMockBuilder(AbstractPlugin::class)
            ->addMethods(['postCreateQuery'])
            ->getMock();
        /* @phpstan-ignore-next-line */
        $observer->expects($this->once())
                 ->method('postCreateQuery')
                 ->with($this->equalTo($expectedEvent));

        $this->client->getEventDispatcher()->addListener(
            Events::POST_CREATE_QUERY,
            [$observer, 'postCreateQuery']
        );

        $this->client->createQuery($type, $options);
    }

    public function testCreateSelect()
    {
        $options = ['optionA' => 1, 'optionB' => 2];

        $observer = $this->getMockBuilder(Client::class)
            ->onlyMethods(['createQuery'])
            ->setConstructorArgs([new MyAdapter(), new EventDispatcher()])
            ->getMock();
        $observer->expects($this->once())
                 ->method('createQuery')
                 ->with($this->equalTo(Client::QUERY_SELECT), $this->equalTo($options))
                 ->willReturn(new SelectQuery());

        $observer->createSelect($options);
    }

    public function testCreateUpdate()
    {
        $options = ['optionA' => 1, 'optionB' => 2];

        $observer = $this->getMockBuilder(Client::class)
            ->onlyMethods(['createQuery'])
            ->setConstructorArgs([new MyAdapter(), new EventDispatcher()])
            ->getMock();
        $observer->expects($this->once())
                 ->method('createQuery')
                 ->with($this->equalTo(Client::QUERY_UPDATE), $this->equalTo($options))
                 ->willReturn(new UpdateQuery());

        $observer->createUpdate($options);
    }

    public function testCreatePing()
    {
        $options = ['optionA' => 1, 'optionB' => 2];

        $observer = $this->getMockBuilder(Client::class)
            ->onlyMethods(['createQuery'])
            ->setConstructorArgs([new MyAdapter(), new EventDispatcher()])
            ->getMock();
        $observer->expects($this->once())
                 ->method('createQuery')
                 ->with($this->equalTo(Client::QUERY_PING), $this->equalTo($options))
                 ->willReturn(new PingQuery());

        $observer->createPing($options);
    }

    public function testCreateMoreLikeThis()
    {
        $options = ['optionA' => 1, 'optionB' => 2];

        $observer = $this->getMockBuilder(Client::class)
            ->onlyMethods(['createQuery'])
            ->setConstructorArgs([new MyAdapter(), new EventDispatcher()])
            ->getMock();
        $observer->expects($this->once())
                 ->method('createQuery')
                 ->with($this->equalTo(Client::QUERY_MORELIKETHIS), $this->equalTo($options))
                 ->willReturn(new MoreLikeThisQuery());

        $observer->createMoreLikeThis($options);
    }

    public function testCreateAnalysisField()
    {
        $options = ['optionA' => 1, 'optionB' => 2];

        $observer = $this->getMockBuilder(Client::class)
            ->onlyMethods(['createQuery'])
            ->setConstructorArgs([new MyAdapter(), new EventDispatcher()])
            ->getMock();
        $observer->expects($this->once())
                 ->method('createQuery')
                 ->with($this->equalTo(Client::QUERY_ANALYSIS_FIELD), $this->equalTo($options))
                 ->willReturn(new AnalysisQueryField());

        $observer->createAnalysisField($options);
    }

    public function testCreateAnalysisDocument()
    {
        $options = ['optionA' => 1, 'optionB' => 2];

        $observer = $this->getMockBuilder(Client::class)
            ->onlyMethods(['createQuery'])
            ->setConstructorArgs([new MyAdapter(), new EventDispatcher()])
            ->getMock();
        $observer->expects($this->once())
                 ->method('createQuery')
                 ->with($this->equalTo(Client::QUERY_ANALYSIS_DOCUMENT), $this->equalTo($options))
                 ->willReturn(new \Solarium\QueryType\Analysis\Query\Document());

        $observer->createAnalysisDocument($options);
    }

    public function testCreateTerms()
    {
        $options = ['optionA' => 1, 'optionB' => 2];

        $observer = $this->getMockBuilder(Client::class)
            ->onlyMethods(['createQuery'])
            ->setConstructorArgs([new MyAdapter(), new EventDispatcher()])
            ->getMock();
        $observer->expects($this->once())
                 ->method('createQuery')
                 ->with($this->equalTo(Client::QUERY_TERMS), $this->equalTo($options))
                 ->willReturn(new TermsQuery());

        $observer->createTerms($options);
    }

    public function testCreateSpellcheck()
    {
        $options = ['optionA' => 1, 'optionB' => 2];

        $observer = $this->getMockBuilder(Client::class)
            ->onlyMethods(['createQuery'])
            ->setConstructorArgs([new MyAdapter(), new EventDispatcher()])
            ->getMock();
        $observer->expects($this->once())
                 ->method('createQuery')
                 ->with($this->equalTo(Client::QUERY_SPELLCHECK), $this->equalTo($options))
                 ->willReturn(new SpellcheckQuery());

        $observer->createSpellcheck($options);
    }

    public function testCreateSuggester()
    {
        $options = ['optionA' => 1, 'optionB' => 2];

        $observer = $this->getMockBuilder(Client::class)
            ->onlyMethods(['createQuery'])
            ->setConstructorArgs([new MyAdapter(), new EventDispatcher()])
            ->getMock();
        $observer->expects($this->once())
                 ->method('createQuery')
                 ->with($this->equalTo(Client::QUERY_SUGGESTER), $this->equalTo($options))
                 ->willReturn(new SuggesterQuery());

        $observer->createSuggester($options);
    }

    public function testCreateExtract()
    {
        $options = ['optionA' => 1, 'optionB' => 2];

        $observer = $this->getMockBuilder(Client::class)
            ->onlyMethods(['createQuery'])
            ->setConstructorArgs([new MyAdapter(), new EventDispatcher()])
            ->getMock();
        $observer->expects($this->once())
                 ->method('createQuery')
                 ->with($this->equalTo(Client::QUERY_EXTRACT), $this->equalTo($options))
                 ->willReturn(new ExtractQuery());

        $observer->createExtract($options);
    }

    public function testCreateStream()
    {
        $options = ['optionA' => 1, 'optionB' => 2];

        $observer = $this->getMockBuilder(Client::class)
            ->onlyMethods(['createQuery'])
            ->setConstructorArgs([new MyAdapter(), new EventDispatcher()])
            ->getMock();
        $observer->expects($this->once())
                 ->method('createQuery')
                 ->with($this->equalTo(Client::QUERY_STREAM), $this->equalTo($options))
                 ->willReturn(new StreamQuery());

        $observer->createStream($options);
    }

    public function testCreateGraph()
    {
        $options = ['optionA' => 1, 'optionB' => 2];

        $observer = $this->getMockBuilder(Client::class)
            ->onlyMethods(['createQuery'])
            ->setConstructorArgs([new MyAdapter(), new EventDispatcher()])
            ->getMock();
        $observer->expects($this->once())
                 ->method('createQuery')
                 ->with($this->equalTo(Client::QUERY_GRAPH), $this->equalTo($options))
                 ->willReturn(new GraphQuery());

        $observer->createGraph($options);
    }

    public function testCreateRealtimeGet()
    {
        $options = ['optionA' => 1, 'optionB' => 2];

        $observer = $this->getMockBuilder(Client::class)
            ->onlyMethods(['createQuery'])
            ->setConstructorArgs([new MyAdapter(), new EventDispatcher()])
            ->getMock();
        $observer->expects($this->once())
                 ->method('createQuery')
                 ->with($this->equalTo(Client::QUERY_REALTIME_GET), $this->equalTo($options))
                 ->willReturn(new RealtimeGetQuery());

        $observer->createRealtimeGet($options);
    }

    public function testCreateCoreAdmin()
    {
        $options = ['optionA' => 1, 'optionB' => 2];

        $observer = $this->getMockBuilder(Client::class)
            ->onlyMethods(['createQuery'])
            ->setConstructorArgs([new MyAdapter(), new EventDispatcher()])
            ->getMock();
        $observer->expects($this->once())
                 ->method('createQuery')
                 ->with($this->equalTo(Client::QUERY_CORE_ADMIN), $this->equalTo($options))
                 ->willReturn(new CoreAdminQuery());

        $observer->createCoreAdmin($options);
    }

    public function testCreateCollections()
    {
        $options = ['optionA' => 1, 'optionB' => 2];

        $observer = $this->getMockBuilder(Client::class)
            ->onlyMethods(['createQuery'])
            ->setConstructorArgs([new MyAdapter(), new EventDispatcher()])
            ->getMock();
        $observer->expects($this->once())
                 ->method('createQuery')
                 ->with($this->equalTo(Client::QUERY_COLLECTIONS), $this->equalTo($options))
                 ->willReturn(new CollectionsQuery());

        $observer->createCollections($options);
    }

    public function testCreateConfigsets()
    {
        $options = ['optionA' => 1, 'optionB' => 2];

        $observer = $this->getMockBuilder(Client::class)
            ->onlyMethods(['createQuery'])
            ->setConstructorArgs([new MyAdapter(), new EventDispatcher()])
            ->getMock();
        $observer->expects($this->once())
                 ->method('createQuery')
                 ->with($this->equalTo(Client::QUERY_CONFIGSETS), $this->equalTo($options))
                 ->willReturn(new ConfigsetsQuery());

        $observer->createConfigsets($options);
    }

    public function testCreateApi()
    {
        $options = ['optionA' => 1, 'optionB' => 2];

        $observer = $this->getMockBuilder(Client::class)
            ->onlyMethods(['createQuery'])
            ->setConstructorArgs([new MyAdapter(), new EventDispatcher()])
            ->getMock();
        $observer->expects($this->once())
                 ->method('createQuery')
                 ->with($this->equalTo(Client::QUERY_API), $this->equalTo($options))
                 ->willReturn(new ApiQuery());

        $observer->createApi($options);
    }

    public function testCreateManagedResources()
    {
        $options = ['optionA' => 1, 'optionB' => 2];

        $observer = $this->getMockBuilder(Client::class)
            ->onlyMethods(['createQuery'])
            ->setConstructorArgs([new MyAdapter(), new EventDispatcher()])
            ->getMock();
        $observer->expects($this->once())
                 ->method('createQuery')
                 ->with($this->equalTo(Client::QUERY_MANAGED_RESOURCES), $this->equalTo($options))
                 ->willReturn(new ManagedResourcesQuery());

        $observer->createManagedResources($options);
    }

    public function testCreateManagedStopwords()
    {
        $options = ['optionA' => 1, 'optionB' => 2];

        $observer = $this->getMockBuilder(Client::class)
            ->onlyMethods(['createQuery'])
            ->setConstructorArgs([new MyAdapter(), new EventDispatcher()])
            ->getMock();
        $observer->expects($this->once())
                 ->method('createQuery')
                 ->with($this->equalTo(Client::QUERY_MANAGED_STOPWORDS), $this->equalTo($options))
                 ->willReturn(new ManagedStopwordsQuery());

        $observer->createManagedStopwords($options);
    }

    public function testCreateManagedSynonyms()
    {
        $options = ['optionA' => 1, 'optionB' => 2];

        $observer = $this->getMockBuilder(Client::class)
            ->onlyMethods(['createQuery'])
            ->setConstructorArgs([new MyAdapter(), new EventDispatcher()])
            ->getMock();
        $observer->expects($this->once())
                 ->method('createQuery')
                 ->with($this->equalTo(Client::QUERY_MANAGED_SYNONYMS), $this->equalTo($options))
                 ->willReturn(new ManagedSynonymsQuery());

        $observer->createManagedSynonyms($options);
    }
}

class MyAdapter extends Http
{
    public function execute(Request $request, Endpoint $endpoint): Response
    {
        return new Response('{}', ['HTTP/1.1 200 OK']);
    }
}

class MyClientPlugin extends AbstractPlugin
{
}
