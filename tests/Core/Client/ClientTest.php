<?php

namespace Solarium\Tests\Core\Client;

use PHPUnit\Framework\TestCase;
use Solarium\Core\Client\Adapter\Http;
use Solarium\Core\Client\Adapter\Http as ClientAdapterHttp;
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
use Solarium\QueryType\MoreLikeThis\Query as MoreLikeThisQuery;
use Solarium\QueryType\Ping\Query as PingQuery;
use Solarium\QueryType\Select\Query\Query;
use Solarium\QueryType\Select\Query\Query as SelectQuery;
use Solarium\QueryType\Suggester\Query as SuggesterQuery;
use Solarium\QueryType\Terms\Query as TermsQuery;
/*
 * @coversDefaultClass \Solarium\Core\Client\Client
 */
use Solarium\QueryType\Update\Query\Query as UpdateQuery;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class ClientTest extends TestCase
{
    /**
     * @var Client
     */
    protected $client;

    public function setUp()
    {
        $this->client = new Client();
    }

    public function testConfigMode()
    {
        $options = [
            'adapter' => MyAdapter::class,
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
        $client = new Client(null, $eventDispatcher);
        $this->assertSame($eventDispatcher, $client->getEventDispatcher());
    }

    public function testConfigModeWithoutKeys()
    {
        $options = [
            'adapter' => MyAdapter::class,
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

    public function testAddEndpointWithDuplicateKey()
    {
        $this->client->createEndpoint('s1');
        $this->expectException(InvalidArgumentException::class);
        $this->client->createEndpoint('s1');
    }

    public function testAddAndGetEndpoints()
    {
        $options = [
            //use array key
            's1' => ['host' => 's1.local'],

            //use key array entry
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

    public function testSetAndGetAdapterWithDefaultAdapter()
    {
        $defaultAdapter = $this->client->getOption('adapter');
        $adapter = $this->client->getAdapter();
        $this->assertThat($adapter, $this->isInstanceOf($defaultAdapter));
    }

    public function testSetAndGetAdapterWithString()
    {
        $adapterClass = MyAdapter::class;
        $this->client->setAdapter($adapterClass);
        $this->assertThat($this->client->getAdapter(), $this->isInstanceOf($adapterClass));
    }

    public function testSetAndGetAdapterWithObject()
    {
        $adapterClass = MyAdapter::class;
        $this->client->setAdapter(new $adapterClass());
        $this->assertThat($this->client->getAdapter(), $this->isInstanceOf($adapterClass));
    }

    public function testSetAndGetAdapterWithInvalidObject()
    {
        $this->expectException(InvalidArgumentException::class);
        $this->client->setAdapter(new \stdClass());
    }

    public function testSetAndGetAdapterWithInvalidString()
    {
        $adapterClass = '\\stdClass';
        $this->client->setAdapter($adapterClass);
        $this->expectException(InvalidArgumentException::class);
        $this->client->getAdapter();
    }

    public function testSetAdapterWithOptions()
    {
        $adapterOptions = [
            'host' => 'myhost',
            'port' => 8080,
            'customOption' => 'foobar',
        ];

        $observer = $this->createMock(Http::class, ['setOptions', 'execute']);
        $observer->expects($this->once())
                 ->method('setOptions')
                 ->with($this->equalTo($adapterOptions));

        $this->client->setOptions(['adapteroptions' => $adapterOptions]);
        $this->client->setAdapter($observer);
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

    public function testRemovePluginAndGetPluginsWithObjectInput()
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
        $queryStub = $this->createMock(Query::class);

        $observer = $this->createMock(AbstractRequestBuilder::class, ['build']);
        $observer->expects($this->once())
                 ->method('build')
                 ->with($this->equalTo($queryStub))
                 ->will($this->returnValue(new Request()));

        $queryStub->expects($this->any())
             ->method('getType')
             ->will($this->returnValue('testquerytype'));
        $queryStub->expects($this->any())
             ->method('getRequestBuilder')
             ->will($this->returnValue($observer));

        $this->client->registerQueryType('testquerytype', Query::class);
        $this->client->createRequest($queryStub);
    }

    public function testCreateRequestInvalidQueryType()
    {
        $queryStub = $this->createMock(Query::class);
        $queryStub->expects($this->any())
             ->method('getType')
             ->will($this->returnValue('testquerytype'));

        $this->expectException(UnexpectedValueException::class);
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
            ->setMethods(['preCreateRequest'])
            ->getMock();
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
            ->setMethods(['postCreateRequest'])
            ->getMock();
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
            ->setMethods(['preCreateResult'])
            ->getMock();

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
            ->setMethods(['postCreateResult'])
            ->getMock();
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

    public function testCreateResultWithInvalidResult()
    {
        $overrideValue = '\\stdClass';
        $response = new Response('', ['HTTP 1.0 200 OK']);

        $mockQuery = $this->getMockBuilder(Query::class)
            ->setMethods(['getResultClass'])
            ->getMock();
        $mockQuery->expects($this->once())
                 ->method('getResultClass')
                 ->will($this->returnValue($overrideValue));

        $this->expectException(UnexpectedValueException::class);
        $this->client->createResult($mockQuery, $response);
    }

    public function testExecute()
    {
        $query = new PingQuery();
        $response = new Response('', ['HTTP 1.0 200 OK']);
        $result = new Result($query, $response);

        $observer = $this->getMockBuilder(Client::class)
            ->setMethods(['createRequest', 'executeRequest', 'createResult'])
            ->getMock();

        $observer->expects($this->once())
                 ->method('createRequest')
                 ->with($this->equalTo($query))
                 ->will($this->returnValue('dummyrequest'));

        $observer->expects($this->once())
                 ->method('executeRequest')
                 ->with($this->equalTo('dummyrequest'))
                 ->will($this->returnValue('dummyresponse'));

        $observer->expects($this->once())
                 ->method('createResult')
                 ->with($this->equalTo($query), $this->equalTo('dummyresponse'))
                 ->will($this->returnValue($result));

        $observer->execute($query);
    }

    public function testExecutePrePlugin()
    {
        $query = new PingQuery();
        $response = new Response('', ['HTTP 1.0 200 OK']);
        $result = new Result($query, $response);
        $expectedEvent = new PreExecuteEvent($query);

        $mock = $this->getMockBuilder(Client::class)
            ->setMethods(['createRequest', 'executeRequest', 'createResult'])
            ->getMock();

        $mock->expects($this->once())
             ->method('createRequest')
             ->will($this->returnValue('dummyrequest'));

        $mock->expects($this->once())
             ->method('executeRequest')
             ->will($this->returnValue('dummyresponse'));

        $mock->expects($this->once())
             ->method('createResult')
             ->will($this->returnValue($result));

        $observer = $this->getMockBuilder(AbstractPlugin::class)
            ->setMethods(['preExecute'])
            ->getMock();
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
            ->setMethods(['createRequest', 'executeRequest', 'createResult'])
            ->getMock();

        $mock->expects($this->once())
             ->method('createRequest')
             ->will($this->returnValue('dummyrequest'));

        $mock->expects($this->once())
             ->method('executeRequest')
             ->will($this->returnValue('dummyresponse'));

        $mock->expects($this->once())
             ->method('createResult')
             ->will($this->returnValue($result));

        $observer = $this->getMockBuilder(AbstractPlugin::class)
            ->setMethods(['postExecute'])
            ->getMock();
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
            ->setMethods(['execute'])
            ->getMock();
        $observer->expects($this->once())
                 ->method('execute')
                 ->with($this->equalTo($request))
                 ->will($this->returnValue($response));

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
            ->setMethods(['execute'])
            ->getMock();
        $mockAdapter->expects($this->once())
                 ->method('execute')
                 ->with($this->equalTo($request))
                 ->will($this->returnValue($response));
        $this->client->setAdapter($mockAdapter);

        $observer = $this->getMockBuilder(AbstractPlugin::class)
            ->setMethods(['preExecuteRequest'])
            ->getMock();
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
            ->setMethods(['execute'])
            ->getMock();
        $mockAdapter->expects($this->any())
                 ->method('execute')
                 ->with($this->equalTo($request))
                 ->will($this->returnValue($response));
        $this->client->setAdapter($mockAdapter);

        $observer = $this->getMockBuilder(AbstractPlugin::class)
            ->setMethods(['postExecuteRequest'])
            ->getMock();
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
            ->setMethods(['execute'])
            ->getMock();
        $observer->expects($this->once())
                 ->method('execute')
                 ->with($this->equalTo($query));

        $observer->ping($query);
    }

    public function testSelect()
    {
        $query = new SelectQuery();

        $observer = $this->getMockBuilder(Client::class)
            ->setMethods(['execute'])
            ->getMock();
        $observer->expects($this->once())
                 ->method('execute')
                 ->with($this->equalTo($query));

        $observer->select($query);
    }

    public function testUpdate()
    {
        $query = new UpdateQuery();

        $observer = $this->getMockBuilder(Client::class)
            ->setMethods(['execute'])
            ->getMock();
        $observer->expects($this->once())
                 ->method('execute')
                 ->with($this->equalTo($query));

        $observer->update($query);
    }

    public function testMoreLikeThis()
    {
        $query = new MoreLikeThisQuery();

        $observer = $this->getMockBuilder(Client::class)
            ->setMethods(['execute'])
            ->getMock();
        $observer->expects($this->once())
                 ->method('execute')
                 ->with($this->equalTo($query));

        $observer->moreLikeThis($query);
    }

    public function testAnalyze()
    {
        $query = new AnalysisQueryField();

        $observer = $this->getMockBuilder(Client::class)
            ->setMethods(['execute'])
            ->getMock();
        $observer->expects($this->once())
                 ->method('execute')
                 ->with($this->equalTo($query));

        $observer->analyze($query);
    }

    public function testTerms()
    {
        $query = new TermsQuery();

        $observer = $this->getMockBuilder(Client::class)
            ->setMethods(['execute'])
            ->getMock();
        $observer->expects($this->once())
                 ->method('execute')
                 ->with($this->equalTo($query));

        $observer->terms($query);
    }

    public function testSuggester()
    {
        $query = new SuggesterQuery();

        $observer = $this->getMockBuilder(Client::class)
            ->setMethods(['execute'])
            ->getMock();
        $observer->expects($this->once())
                 ->method('execute')
                 ->with($this->equalTo($query));

        $observer->suggester($query);
    }

    public function testExtract()
    {
        $query = new ExtractQuery();

        $observer = $this->getMockBuilder(Client::class)
            ->setMethods(['execute'])
            ->getMock();
        $observer->expects($this->once())
                 ->method('execute')
                 ->with($this->equalTo($query));

        $observer->extract($query);
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
            ->setMethods(['preCreateQuery'])
            ->getMock();
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
            ->setMethods(['postCreateQuery'])
            ->getMock();
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
            ->setMethods(['createQuery'])
            ->getMock();
        $observer->expects($this->once())
                 ->method('createQuery')
                 ->with($this->equalTo(Client::QUERY_SELECT), $this->equalTo($options));

        $observer->createSelect($options);
    }

    public function testCreateUpdate()
    {
        $options = ['optionA' => 1, 'optionB' => 2];

        $observer = $this->getMockBuilder(Client::class)
            ->setMethods(['createQuery'])
            ->getMock();
        $observer->expects($this->once())
                 ->method('createQuery')
                 ->with($this->equalTo(Client::QUERY_UPDATE), $this->equalTo($options));

        $observer->createUpdate($options);
    }

    public function testCreatePing()
    {
        $options = ['optionA' => 1, 'optionB' => 2];

        $observer = $this->getMockBuilder(Client::class)
            ->setMethods(['createQuery'])
            ->getMock();
        $observer->expects($this->once())
                 ->method('createQuery')
                 ->with($this->equalTo(Client::QUERY_PING), $this->equalTo($options));

        $observer->createPing($options);
    }

    public function testCreateMoreLikeThis()
    {
        $options = ['optionA' => 1, 'optionB' => 2];

        $observer = $this->getMockBuilder(Client::class)
            ->setMethods(['createQuery'])
            ->getMock();
        $observer->expects($this->once())
                 ->method('createQuery')
                 ->with($this->equalTo(Client::QUERY_MORELIKETHIS), $this->equalTo($options));

        $observer->createMoreLikeThis($options);
    }

    public function testCreateAnalysisField()
    {
        $options = ['optionA' => 1, 'optionB' => 2];

        $observer = $this->getMockBuilder(Client::class)
            ->setMethods(['createQuery'])
            ->getMock();
        $observer->expects($this->once())
                 ->method('createQuery')
                 ->with($this->equalTo(Client::QUERY_ANALYSIS_FIELD), $this->equalTo($options));

        $observer->createAnalysisField($options);
    }

    public function testCreateAnalysisDocument()
    {
        $options = ['optionA' => 1, 'optionB' => 2];

        $observer = $this->getMockBuilder(Client::class)
            ->setMethods(['createQuery'])
            ->getMock();
        $observer->expects($this->once())
                 ->method('createQuery')
                 ->with($this->equalTo(Client::QUERY_ANALYSIS_DOCUMENT), $this->equalTo($options));

        $observer->createAnalysisDocument($options);
    }

    public function testCreateTerms()
    {
        $options = ['optionA' => 1, 'optionB' => 2];

        $observer = $this->getMockBuilder(Client::class)
            ->setMethods(['createQuery'])
            ->getMock();
        $observer->expects($this->once())
                 ->method('createQuery')
                 ->with($this->equalTo(Client::QUERY_TERMS), $this->equalTo($options));

        $observer->createTerms($options);
    }

    public function testCreateSuggester()
    {
        $options = ['optionA' => 1, 'optionB' => 2];

        $observer = $this->getMockBuilder(Client::class)
            ->setMethods(['createQuery'])
            ->getMock();
        $observer->expects($this->once())
                 ->method('createQuery')
                 ->with($this->equalTo(Client::QUERY_SUGGESTER), $this->equalTo($options));

        $observer->createSuggester($options);
    }

    public function testCreateExtract()
    {
        $options = ['optionA' => 1, 'optionB' => 2];

        $observer = $this->getMockBuilder(Client::class)
            ->setMethods(['createQuery'])
            ->getMock();
        $observer->expects($this->once())
                 ->method('createQuery')
                 ->with($this->equalTo(Client::QUERY_EXTRACT), $this->equalTo($options));

        $observer->createExtract($options);
    }
}

class MyAdapter extends ClientAdapterHttp
{
    public function execute($request, $endpoint)
    {
        return new Response('{}', ['HTTP/1.1 200 OK']);
    }
}

class MyClientPlugin extends AbstractPlugin
{
}
