<?php
/**
 * Copyright 2011 Bas de Nooijer. All rights reserved.
 *
 * Redistribution and use in source and binary forms, with or without
 * modification, are permitted provided that the following conditions are met:
 *
 * 1. Redistributions of source code must retain the above copyright notice,
 *    this list of conditions and the following disclaimer.
 *
 * 2. Redistributions in binary form must reproduce the above copyright notice,
 *    this listof conditions and the following disclaimer in the documentation
 *    and/or other materials provided with the distribution.
 *
 * THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDER AND CONTRIBUTORS "AS IS"
 * AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO, THE
 * IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE
 * ARE DISCLAIMED. IN NO EVENT SHALL THE COPYRIGHT HOLDER OR CONTRIBUTORS BE
 * LIABLE FOR ANY DIRECT, INDIRECT, INCIDENTAL, SPECIAL, EXEMPLARY, OR
 * CONSEQUENTIAL DAMAGES (INCLUDING, BUT NOT LIMITED TO, PROCUREMENT OF
 * SUBSTITUTE GOODS OR SERVICES; LOSS OF USE, DATA, OR PROFITS; OR BUSINESS
 * INTERRUPTION) HOWEVER CAUSED AND ON ANY THEORY OF LIABILITY, WHETHER IN
 * CONTRACT, STRICT LIABILITY, OR TORT (INCLUDING NEGLIGENCE OR OTHERWISE)
 * ARISING IN ANY WAY OUT OF THE USE OF THIS SOFTWARE, EVEN IF ADVISED OF THE
 * POSSIBILITY OF SUCH DAMAGE.
 *
 * The views and conclusions contained in the software and documentation are
 * those of the authors and should not be interpreted as representing official
 * policies, either expressed or implied, of the copyright holder.
 */

namespace Solarium\Tests\Core\Client;

use Solarium\Core\Client\Client;
use Solarium\Core\Client\Request;
use Solarium\Core\Client\Response;
use Solarium\Core\Query\Result\Result;
use Solarium\QueryType\Select\Query\Query as SelectQuery;
use Solarium\QueryType\Ping\Query as PingQuery;
use Solarium\QueryType\MoreLikeThis\Query as MoreLikeThisQuery;
use Solarium\QueryType\Update\Query\Query as UpdateQuery;
use Solarium\QueryType\Analysis\Query\Field as AnalysisQueryField;
use Solarium\QueryType\Terms\Query as TermsQuery;
use Solarium\QueryType\Suggester\Query as SuggesterQuery;
use Solarium\QueryType\Extract\Query as ExtractQuery;
use Solarium\Core\Client\Adapter\Http as ClientAdapterHttp;
use Solarium\Core\Plugin\AbstractPlugin;
use Solarium\Core\Event\Events;
use Solarium\Core\Event\PreCreateRequest as PreCreateRequestEvent;
use Solarium\Core\Event\PostCreateRequest as PostCreateRequestEvent;
use Solarium\Core\Event\PreCreateQuery as PreCreateQueryEvent;
use Solarium\Core\Event\PostCreateQuery as PostCreateQueryEvent;
use Solarium\Core\Event\PreCreateResult as PreCreateResultEvent;
use Solarium\Core\Event\PostCreateResult as PostCreateResultEvent;
use Solarium\Core\Event\PreExecute as PreExecuteEvent;
use Solarium\Core\Event\PostExecute as PostExecuteEvent;
use Solarium\Core\Event\PreExecuteRequest as PreExecuteRequestEvent;
use Solarium\Core\Event\PostExecuteRequest as PostExecuteRequestEvent;

/**
 * @coversDefaultClass \Solarium\Core\Client\Client
 */
class ClientTest extends \PHPUnit_Framework_TestCase
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
        $options = array(
            'adapter' => __NAMESPACE__.'\\MyAdapter',
            'endpoint' => array(
                'myhost' => array(
                    'host' => 'myhost',
                    'port' => 8080,
                ),
            ),
            'querytype' => array(
                'myquerytype' => 'MyQuery',
            ),
            'plugin' => array(
                'myplugin' => array(
                    'plugin' => __NAMESPACE__.'\\MyClientPlugin',
                    'options' => array(
                        'option1' => 'value1',
                        'option2' => 'value2',
                    )
                )
            ),
        );

        $this->client->setOptions($options);

        $adapter = $this->client->getAdapter();

        $this->assertThat($adapter, $this->isInstanceOf(__NAMESPACE__.'\\MyAdapter'));
        $this->assertEquals(8080, $this->client->getEndpoint('myhost')->getPort());

        $queryTypes = $this->client->getQueryTypes();
        $this->assertEquals(
            $options['querytype']['myquerytype'],
            $queryTypes['myquerytype']
        );

        $plugin = $this->client->getPlugin('myplugin');
        $this->assertThat($plugin, $this->isInstanceOf(__NAMESPACE__.'\\MyClientPlugin'));
        $this->assertEquals($options['plugin']['myplugin']['options'], $plugin->getOptions());

    }

    /**
     * @covers ::getEventDispatcher
     * @covers ::setEventDispatcher
     */
    public function testGetEventDispatcher() {
      $this->assertInstanceOf('\Symfony\Component\EventDispatcher\EventDispatcherInterface', $this->client->getEventDispatcher());
        $eventDispatcher = $this->getMock('\Symfony\Component\EventDispatcher\EventDispatcherInterface');
      $this->client->setEventDispatcher($eventDispatcher);
      $this->assertSame($eventDispatcher, $this->client->getEventDispatcher());
    }

    public function testEventDispatcherInjection()
    {
        $eventDispatcher = $this->getMock('\Symfony\Component\EventDispatcher\EventDispatcherInterface');
        $client = new Client(null, $eventDispatcher);
        $this->assertSame($eventDispatcher, $client->getEventDispatcher());
    }

    public function testConfigModeWithoutKeys()
    {
        $options = array(
            'adapter' => __NAMESPACE__.'\\MyAdapter',
            'endpoint' => array(
                array(
                    'key' => 'myhost',
                    'host' => 'myhost',
                    'port' => 8080,
                ),
            ),
            'querytype' => array(
                array(
                    'type' => 'myquerytype',
                    'query' => 'MyQuery',
                )
            ),
            'plugin' => array(
                 array(
                    'key' => 'myplugin',
                    'plugin' => __NAMESPACE__.'\\MyClientPlugin',
                    'options' => array(
                        'option1' => 'value1',
                        'option2' => 'value2',
                    )
                )
            ),
        );

        $this->client->setOptions($options);

        $adapter = $this->client->getAdapter();

        $this->assertThat($adapter, $this->isInstanceOf(__NAMESPACE__.'\\MyAdapter'));
        $this->assertEquals(8080, $this->client->getEndpoint('myhost')->getPort());

        $queryTypes = $this->client->getQueryTypes();
        $this->assertEquals(
            'MyQuery',
            $queryTypes['myquerytype']
        );

        $plugin = $this->client->getPlugin('myplugin');
        $this->assertThat($plugin, $this->isInstanceOf(__NAMESPACE__.'\\MyClientPlugin'));
        $this->assertEquals($options['plugin'][0]['options'], $plugin->getOptions());
    }

    public function testCreateEndpoint()
    {
        $endpoint = $this->client->createEndpoint();
        $this->assertEquals(null, $endpoint->getKey());
        $this->assertThat($endpoint, $this->isInstanceOf('Solarium\Core\Client\Endpoint'));
    }

    public function testCreateEndpointWithKey()
    {
        $endpoint = $this->client->createEndpoint('key1');
        $this->assertEquals('key1', $endpoint->getKey());
        $this->assertThat($endpoint, $this->isInstanceOf('Solarium\Core\Client\Endpoint'));
    }

    public function testCreateEndpointWithSetAsDefault()
    {
        $this->client->createEndpoint('key3', true);
        $endpoint = $this->client->getEndpoint();
        $this->assertEquals('key3', $endpoint->getKey());
    }

    public function testCreateEndpointWithArray()
    {
        $options = array(
            'key' => 'server2',
            'host' => 's2.local',
        );

        $endpoint = $this->client->createEndpoint($options);
        $this->assertEquals('server2', $endpoint->getKey());
        $this->assertEquals('s2.local', $endpoint->getHost());
        $this->assertThat($endpoint, $this->isInstanceOf('Solarium\Core\Client\Endpoint'));
    }

    public function testAddAndGetEndpoint()
    {
        $endpoint = $this->client->createEndpoint();
        $endpoint->setKey('s3');
        $endpoint->setHost('s3.local');
        $this->client->clearEndpoints();
        $this->client->addEndpoint($endpoint);

        $this->assertEquals(
            array('s3' => $endpoint),
            $this->client->getEndpoints()
        );

        // check default endpoint
        $this->assertEquals(
            $endpoint,
            $this->client->getEndpoint()
        );
    }

    public function testAddEndpointWithArray()
    {
        $options = array(
            'key' => 'server2',
            'host' => 's2.local',
        );

        $endpoint = $this->client->createEndpoint($options);
        $this->client->addEndpoint($endpoint);

        $this->assertEquals(
            $endpoint,
            $this->client->getEndpoint('server2')
        );
    }

    public function testAddEndpointWithoutKey()
    {
        $endpoint = $this->client->createEndpoint();

        $this->setExpectedException('Solarium\Exception\InvalidArgumentException');
        $this->client->addEndpoint($endpoint);
    }

    public function testAddEndpointWithDuplicateKey()
    {
        $this->client->createEndpoint('s1');
        $this->setExpectedException('Solarium\Exception\InvalidArgumentException');
        $this->client->createEndpoint('s1');
    }

    public function testAddAndGetEndpoints()
    {
        $options = array(
            //use array key
            's1' => array('host' => 's1.local'),

            //use key array entry
            array('key' => 's2', 'host' => 's2.local'),
        );

        $this->client->addEndpoints($options);
        $endpoints = $this->client->getEndpoints();

        $this->assertEquals('s1.local', $endpoints['s1']->getHost());
        $this->assertEquals('s2.local', $endpoints['s2']->getHost());
    }

    public function testGetEndpointWithInvalidKey()
    {
        $this->client->createEndpoint('s1');

        $this->setExpectedException('Solarium\Exception\OutOfBoundsException');
        $this->client->getEndpoint('s2');
    }

    public function testSetAndGetEndpoints()
    {
        $endpoint1 = $this->client->createEndpoint('s1');
        $this->client->addEndpoint($endpoint1);

        $endpoint2 = $this->client->createEndpoint('s2');
        $endpoint3 = $this->client->createEndpoint('s3');
        $this->client->setEndpoints(array($endpoint2, $endpoint3));

        $this->assertEquals(
            array('s2' => $endpoint2, 's3' => $endpoint3),
            $this->client->getEndpoints()
        );
    }

    public function testRemoveEndpointWithKey()
    {
        $endpoint1 = $this->client->createEndpoint('s1');
        $endpoint2 = $this->client->createEndpoint('s2');
        $endpoint3 = $this->client->createEndpoint('s3');
        $this->client->setEndpoints(array($endpoint1, $endpoint2, $endpoint3));
        $this->client->removeEndpoint('s1');

        $this->assertEquals(
            array('s2' => $endpoint2, 's3' => $endpoint3),
            $this->client->getEndpoints()
        );
    }

    public function testRemoveEndpointWithObject()
    {
        $endpoint1 = $this->client->createEndpoint('s1');
        $endpoint2 = $this->client->createEndpoint('s2');
        $endpoint3 = $this->client->createEndpoint('s3');
        $this->client->setEndpoints(array($endpoint1, $endpoint2, $endpoint3));
        $this->client->removeEndpoint($endpoint1);

        $this->assertEquals(
            array('s2' => $endpoint2, 's3' => $endpoint3),
            $this->client->getEndpoints()
        );
    }

    public function testClearEndpoints()
    {
        $endpoint1 = $this->client->createEndpoint('s1');
        $endpoint2 = $this->client->createEndpoint('s2');

        $this->client->setEndpoints(array($endpoint1, $endpoint2));
        $this->client->clearEndpoints();

        $this->assertEquals(
            array(),
            $this->client->getEndpoints()
        );
    }

    public function testSetDefaultEndpointWithKey()
    {
        $endpoint1 = $this->client->createEndpoint('s1');
        $endpoint2 = $this->client->createEndpoint('s2');

        $this->client->setEndpoints(array($endpoint1, $endpoint2));

        $this->assertEquals($endpoint1, $this->client->getEndpoint());
        $this->client->setDefaultEndpoint('s2');
        $this->assertEquals($endpoint2, $this->client->getEndpoint());
    }

    public function testSetDefaultEndpointWithObject()
    {
        $endpoint1 = $this->client->createEndpoint('s1');
        $endpoint2 = $this->client->createEndpoint('s2');

        $this->client->setEndpoints(array($endpoint1, $endpoint2));

        $this->assertEquals($endpoint1, $this->client->getEndpoint());
        $this->client->setDefaultEndpoint($endpoint2);
        $this->assertEquals($endpoint2, $this->client->getEndpoint());
    }

    public function testSetDefaultEndpointWithInvalidKey()
    {
        $this->setExpectedException('Solarium\Exception\OutOfBoundsException');
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
        $adapterClass = __NAMESPACE__.'\\MyAdapter';
        $this->client->setAdapter($adapterClass);
        $this->assertThat($this->client->getAdapter(), $this->isInstanceOf($adapterClass));
    }

    public function testSetAndGetAdapterWithObject()
    {
        $adapterClass = __NAMESPACE__.'\\MyAdapter';
        $this->client->setAdapter(new $adapterClass);
        $this->assertThat($this->client->getAdapter(), $this->isInstanceOf($adapterClass));
    }

    public function testSetAndGetAdapterWithInvalidObject()
    {
        $this->setExpectedException('Solarium\Exception\InvalidArgumentException');
        $this->client->setAdapter(new \stdClass());
    }

    public function testSetAndGetAdapterWithInvalidString()
    {
        $adapterClass = '\\stdClass';
        $this->client->setAdapter($adapterClass);
        $this->setExpectedException('Solarium\Exception\InvalidArgumentException');
        $this->client->getAdapter();
    }

    public function testSetAdapterWithOptions()
    {
        $adapterOptions = array(
            'host' => 'myhost',
            'port' => 8080,
            'customOption' => 'foobar',
        );

        $observer = $this->getMock('Solarium\Core\Client\Adapter\Http', array('setOptions', 'execute'));
        $observer->expects($this->once())
                 ->method('setOptions')
                 ->with($this->equalTo($adapterOptions));

        $this->client->setOptions(array('adapteroptions' => $adapterOptions));
        $this->client->setAdapter($observer);
    }

    public function testRegisterQueryTypeAndGetQueryTypes()
    {
        $queryTypes = $this->client->getQueryTypes();

        $this->client->registerQueryType('myquerytype', 'myquery');

        $queryTypes['myquerytype'] = 'myquery';

        $this->assertEquals(
            $queryTypes,
            $this->client->getQueryTypes()
        );
    }

    public function testRegisterAndGetPlugin()
    {
        $options = array('option1' => 1);
        $this->client->registerPlugin('testplugin', __NAMESPACE__.'\\MyClientPlugin', $options);

        $plugin = $this->client->getPlugin('testplugin');

        $this->assertThat(
            $plugin,
            $this->isInstanceOf(__NAMESPACE__.'\\MyClientPlugin')
        );

        $this->assertEquals(
            $options,
            $plugin->getOptions()
        );
    }

    public function testRegisterInvalidPlugin()
    {
        $this->setExpectedException('Solarium\Exception\InvalidArgumentException');
        $this->client->registerPlugin('testplugin', 'StdClass');
    }

    public function testGetInvalidPlugin()
    {
        $this->assertEquals(
            null,
            $this->client->getPlugin('invalidplugin', false)
        );
    }

    public function testAutoloadPlugin()
    {
        $loadbalancer = $this->client->getPlugin('loadbalancer');
        $this->assertThat(
            $loadbalancer,
            $this->isInstanceOf('Solarium\Plugin\Loadbalancer\Loadbalancer')
        );
    }

    public function testAutoloadInvalidPlugin()
    {
        $this->setExpectedException('Solarium\Exception\OutOfBoundsException');
        $this->client->getPlugin('invalidpluginname');
    }

    public function testRemoveAndGetPlugins()
    {
        $options = array('option1' => 1);
        $this->client->registerPlugin('testplugin', __NAMESPACE__.'\\MyClientPlugin', $options);

        $plugin = $this->client->getPlugin('testplugin');
        $plugins = $this->client->getPlugins();

        $this->assertEquals(
            array('testplugin' => $plugin),
            $plugins
        );

        $this->client->removePlugin('testplugin');
        $plugins = $this->client->getPlugins();

        $this->assertEquals(
            array(),
            $plugins
        );
    }

    public function testRemovePluginAndGetPluginsWithObjectInput()
    {
        $options = array('option1' => 1);
        $this->client->registerPlugin('testplugin', __NAMESPACE__.'\\MyClientPlugin', $options);

        $plugin = $this->client->getPlugin('testplugin');
        $plugins = $this->client->getPlugins();

        $this->assertEquals(
            array('testplugin' => $plugin),
            $plugins
        );

        $this->client->removePlugin($plugin);
        $plugins = $this->client->getPlugins();

        $this->assertEquals(
            array(),
            $plugins
        );
    }

    public function testCreateRequest()
    {
        $queryStub = $this->getMock('Solarium\QueryType\Select\Query\Query');

        $observer = $this->getMock('Solarium\Core\Query\AbstractRequestBuilder', array('build'));
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

        $this->client->registerQueryType('testquerytype', 'Solarium\QueryType\Select\Query\Query', $observer, '');
        $this->client->createRequest($queryStub);
    }

    public function testCreateRequestInvalidQueryType()
    {
        $queryStub = $this->getMock('Solarium\QueryType\Select\Query\Query');
        $queryStub->expects($this->any())
             ->method('getType')
             ->will($this->returnValue('testquerytype'));

        $this->setExpectedException('Solarium\Exception\UnexpectedValueException');
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

        $observer = $this->getMock('Solarium\Core\Plugin\AbstractPlugin', array('preCreateRequest'));
        $observer->expects($this->once())
                 ->method('preCreateRequest')
                 ->with($this->equalTo($expectedEvent));

        $this->client->registerPlugin('testplugin', $observer);
        $this->client->getEventDispatcher()->addListener(
            Events::PRE_CREATE_REQUEST,
            array($observer, 'preCreateRequest')
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

        $observer = $this->getMock('Solarium\Core\Plugin\AbstractPlugin', array('postCreateRequest'));
        $observer->expects($this->once())
                 ->method('postCreateRequest')
                 ->with($this->equalTo($expectedEvent));

        $this->client->registerPlugin('testplugin', $observer);
        $this->client->getEventDispatcher()->addListener(
            Events::POST_CREATE_REQUEST,
            array($observer, 'postCreateRequest')
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

        $this->assertEquals(
            $expectedRequest,
            $returnedRequest
        );
    }

    public function testCreateResult()
    {
        $query = new SelectQuery();
        $response = new Response('', array('HTTP 1.0 200 OK'));
        $result = $this->client->createResult($query, $response);

        $this->assertThat(
            $result,
            $this->isInstanceOf($query->getResultClass())
        );
    }

    public function testCreateResultPrePlugin()
    {
        $query = new SelectQuery();
        $response = new Response('', array('HTTP 1.0 200 OK'));
        $expectedEvent = new PreCreateResultEvent($query, $response);
        if (method_exists($expectedEvent, 'setDispatcher')) {
            $expectedEvent->setDispatcher($this->client->getEventDispatcher());
            $expectedEvent->setName(Events::PRE_CREATE_RESULT);
        }

        $observer = $this->getMock('Solarium\Core\Plugin\AbstractPlugin', array('preCreateResult'));
        $observer->expects($this->once())
                 ->method('preCreateResult')
                 ->with($this->equalTo($expectedEvent));

        $this->client->registerPlugin('testplugin', $observer);
        $this->client->getEventDispatcher()->addListener(
            Events::PRE_CREATE_RESULT,
            array($observer, 'preCreateResult')
        );

        $this->client->createResult($query, $response);
    }

    public function testCreateResultPostPlugin()
    {
        $query = new SelectQuery();
        $response = new Response('', array('HTTP 1.0 200 OK'));
        $result = $this->client->createResult($query, $response);
        $expectedEvent = new PostCreateResultEvent($query, $response, $result);
        if (method_exists($expectedEvent, 'setDispatcher')) {
            $expectedEvent->setDispatcher($this->client->getEventDispatcher());
            $expectedEvent->setName(Events::POST_CREATE_RESULT);
        }

        $observer = $this->getMock('Solarium\Core\Plugin\AbstractPlugin', array('postCreateResult'));
        $observer->expects($this->once())
                 ->method('postCreateResult')
                 ->with($this->equalTo($expectedEvent));

        $this->client->registerPlugin('testplugin', $observer);
        $this->client->getEventDispatcher()->addListener(
            Events::POST_CREATE_RESULT,
            array($observer, 'postCreateResult')
        );

        $this->client->createResult($query, $response);
    }

    public function testCreateResultWithOverridingPlugin()
    {
        $query = new SelectQuery();
        $response = new Response('test 1234', array('HTTP 1.0 200 OK'));
        $expectedEvent = new PreCreateResultEvent($query, $response);
        if (method_exists($expectedEvent, 'setDispatcher')) {
            $expectedEvent->setDispatcher($this->client->getEventDispatcher());
            $expectedEvent->setName(Events::PRE_CREATE_RESULT);
        }
        $expectedResult = new Result($this->client, $query, $response);

        $test = $this;
        $this->client->getEventDispatcher()->addListener(
            Events::PRE_CREATE_RESULT,
            function (PreCreateResultEvent $event) use ($test, $expectedResult, $expectedEvent) {
                $test->assertEquals($expectedEvent, $event);
                $event->setResult($expectedResult);
            }
        );

        $returnedResult = $this->client->createResult($query, $response);

        $this->assertEquals(
            $expectedResult,
            $returnedResult
        );
    }

    public function testCreateResultWithInvalidResult()
    {
        $overrideValue =  '\\stdClass';
        $response = new Response('', array('HTTP 1.0 200 OK'));

        $mockQuery = $this->getMock('Solarium\QueryType\Select\Query\Query', array('getResultClass'));
        $mockQuery->expects($this->once())
                 ->method('getResultClass')
                 ->will($this->returnValue($overrideValue));

        $this->setExpectedException('Solarium\Exception\UnexpectedValueException');
        $this->client->createResult($mockQuery, $response);
    }

    public function testExecute()
    {
        $query = new PingQuery();
        $response = new Response('', array('HTTP 1.0 200 OK'));
        $result = new Result($this->client, $query, $response);

        $observer = $this->getMock(
            'Solarium\Core\Client\Client',
            array('createRequest', 'executeRequest', 'createResult')
        );

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
        $response = new Response('', array('HTTP 1.0 200 OK'));
        $result = new Result($this->client, $query, $response);
        $expectedEvent = new PreExecuteEvent($query);


        $mock = $this->getMock('Solarium\Core\Client\Client', array('createRequest', 'executeRequest', 'createResult'));

        $mock->expects($this->once())
             ->method('createRequest')
             ->will($this->returnValue('dummyrequest'));

        $mock->expects($this->once())
             ->method('executeRequest')
             ->will($this->returnValue('dummyresponse'));

        $mock->expects($this->once())
             ->method('createResult')
             ->will($this->returnValue($result));

        $observer = $this->getMock('Solarium\Core\Plugin\AbstractPlugin', array('preExecute'));
        $observer->expects($this->once())
                 ->method('preExecute')
                 ->with($this->equalTo($expectedEvent));

        $mock->getEventDispatcher()->addListener(Events::PRE_EXECUTE, array($observer, 'preExecute'));

        if (method_exists($expectedEvent, 'setDispatcher')) {
            $expectedEvent->setName(Events::PRE_EXECUTE);
            $expectedEvent->setDispatcher($mock->getEventDispatcher());
        }

        $mock->execute($query);
    }

    public function testExecutePostPlugin()
    {
        $query = new PingQuery();
        $response = new Response('', array('HTTP 1.0 200 OK'));
        $result = new Result($this->client, $query, $response);
        $expectedEvent = new PostExecuteEvent($query, $result);

        $mock = $this->getMock('Solarium\Core\Client\Client', array('createRequest', 'executeRequest', 'createResult'));

        $mock->expects($this->once())
             ->method('createRequest')
             ->will($this->returnValue('dummyrequest'));

        $mock->expects($this->once())
             ->method('executeRequest')
             ->will($this->returnValue('dummyresponse'));

        $mock->expects($this->once())
             ->method('createResult')
             ->will($this->returnValue($result));

        $observer = $this->getMock('Solarium\Core\Plugin\Plugin', array('postExecute'));
        $observer->expects($this->once())
                 ->method('postExecute')
                 ->with($this->equalTo($expectedEvent));

        $mock->getEventDispatcher()->addListener(Events::POST_EXECUTE, array($observer, 'postExecute'));

        if (method_exists($expectedEvent, 'setDispatcher')) {
            $expectedEvent->setName(Events::POST_EXECUTE);
            $expectedEvent->setDispatcher($mock->getEventDispatcher());
        }

        $mock->execute($query);
    }

    public function testExecuteWithOverridingPlugin()
    {
        $query = new PingQuery();
        $response = new Response('', array('HTTP 1.0 200 OK'));
        $expectedResult = new Result($this->client, $query, $response);
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

        $this->assertEquals(
            $expectedResult,
            $returnedResult
        );
    }

    public function testExecuteRequest()
    {
        $request = new Request();
        $response = new Response('', array('HTTP 1.0 200 OK'));

        $observer = $this->getMock('Solarium\Core\Client\Adapter\Http', array('execute'));
        $observer->expects($this->once())
                 ->method('execute')
                 ->with($this->equalTo($request))
                 ->will($this->returnValue($response));

        $this->client->setAdapter($observer);
        $returnedResponse = $this->client->executeRequest($request);

        $this->assertEquals(
            $response,
            $returnedResponse
        );
    }

    public function testExecuteRequestPrePlugin()
    {
        $request = new Request();
        $endpoint = $this->client->createEndpoint('s1');
        $response = new Response('', array('HTTP 1.0 200 OK'));
        $expectedEvent = new PreExecuteRequestEvent($request, $endpoint);
        if (method_exists($expectedEvent, 'setDispatcher')) {
            $expectedEvent->setDispatcher($this->client->getEventDispatcher());
            $expectedEvent->setName(Events::PRE_EXECUTE_REQUEST);
        }

        $mockAdapter = $this->getMock('Solarium\Core\Client\Adapter\Http', array('execute'));
        $mockAdapter->expects($this->once())
                 ->method('execute')
                 ->with($this->equalTo($request))
                 ->will($this->returnValue($response));
        $this->client->setAdapter($mockAdapter);

        $observer = $this->getMock('Solarium\Core\Plugin\AbstractPlugin', array('preExecuteRequest'));
        $observer->expects($this->once())
                 ->method('preExecuteRequest')
                 ->with($this->equalTo($expectedEvent));

        $this->client->getEventDispatcher()->addListener(
            Events::PRE_EXECUTE_REQUEST,
            array($observer, 'preExecuteRequest')
        );
        $this->client->executeRequest($request, $endpoint);
    }

    public function testExecuteRequestPostPlugin()
    {
        $request = new Request();
        $endpoint = $this->client->createEndpoint('s1');
        $response = new Response('', array('HTTP 1.0 200 OK'));
        $expectedEvent = new PostExecuteRequestEvent($request, $endpoint, $response);
        if (method_exists($expectedEvent, 'setDispatcher')) {
            $expectedEvent->setDispatcher($this->client->getEventDispatcher());
            $expectedEvent->setName(Events::POST_EXECUTE_REQUEST);
        }

        $mockAdapter = $this->getMock('Solarium\Core\Client\Adapter\Http', array('execute'));
        $mockAdapter->expects($this->any())
                 ->method('execute')
                 ->with($this->equalTo($request))
                 ->will($this->returnValue($response));
        $this->client->setAdapter($mockAdapter);

        $observer = $this->getMock('Solarium\Core\Plugin\AbstractPlugin', array('postExecuteRequest'));
        $observer->expects($this->once())
                 ->method('postExecuteRequest')
                 ->with($this->equalTo($expectedEvent));

        $this->client->getEventDispatcher()->addListener(
            Events::POST_EXECUTE_REQUEST,
            array($observer, 'postExecuteRequest')
        );
        $this->client->executeRequest($request, $endpoint);
    }

    public function testExecuteRequestWithOverridingPlugin()
    {
        $request = new Request();
        $response = new Response('', array('HTTP 1.0 200 OK'));
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

        $this->assertEquals(
            $response,
            $returnedResponse
        );
    }

    public function testPing()
    {
        $query = new PingQuery();

        $observer = $this->getMock('Solarium\Core\Client\Client', array('execute'));
        $observer->expects($this->once())
                 ->method('execute')
                 ->with($this->equalTo($query));

        $observer->ping($query);
    }

    public function testSelect()
    {
        $query = new SelectQuery();

        $observer = $this->getMock('Solarium\Core\Client\Client', array('execute'));
        $observer->expects($this->once())
                 ->method('execute')
                 ->with($this->equalTo($query));

        $observer->select($query);
    }

    public function testUpdate()
    {
        $query = new UpdateQuery();

        $observer = $this->getMock('Solarium\Core\Client\Client', array('execute'));
        $observer->expects($this->once())
                 ->method('execute')
                 ->with($this->equalTo($query));

        $observer->update($query);
    }

    public function testMoreLikeThis()
    {
        $query = new MoreLikeThisQuery();

        $observer = $this->getMock('Solarium\Core\Client\Client', array('execute'));
        $observer->expects($this->once())
                 ->method('execute')
                 ->with($this->equalTo($query));

        $observer->moreLikeThis($query);
    }

    public function testAnalyze()
    {
        $query = new AnalysisQueryField();

        $observer = $this->getMock('Solarium\Core\Client\Client', array('execute'));
        $observer->expects($this->once())
                 ->method('execute')
                 ->with($this->equalTo($query));

        $observer->analyze($query);
    }

    public function testTerms()
    {
        $query = new TermsQuery();

        $observer = $this->getMock('Solarium\Core\Client\Client', array('execute'));
        $observer->expects($this->once())
                 ->method('execute')
                 ->with($this->equalTo($query));

        $observer->terms($query);
    }

    public function testSuggester()
    {
        $query = new SuggesterQuery();

        $observer = $this->getMock('Solarium\Core\Client\Client', array('execute'));
        $observer->expects($this->once())
                 ->method('execute')
                 ->with($this->equalTo($query));

        $observer->suggester($query);
    }

    public function testExtract()
    {
        $query = new ExtractQuery();

        $observer = $this->getMock('Solarium\Core\Client\Client', array('execute'));
        $observer->expects($this->once())
                 ->method('execute')
                 ->with($this->equalTo($query));

        $observer->extract($query);
    }

    public function testCreateQuery()
    {
        $options = array('optionA' => 1, 'optionB' => 2);
        $query = $this->client->createQuery(Client::QUERY_SELECT, $options);

        // check class mapping
        $this->assertThat($query, $this->isInstanceOf('Solarium\QueryType\Select\Query\Query'));

        // check option forwarding
        $queryOptions = $query->getOptions();
        $this->assertEquals(
            $options['optionB'],
            $queryOptions['optionB']
        );
    }

    public function testCreateQueryWithInvalidQueryType()
    {
        $this->setExpectedException('Solarium\Exception\InvalidArgumentException');
        $this->client->createQuery('invalidtype');
    }

    public function testCreateQueryWithInvalidClass()
    {
        $this->client->registerQueryType('invalidquery', '\\StdClass');
        $this->setExpectedException('Solarium\Exception\UnexpectedValueException');
        $this->client->createQuery('invalidquery');
    }

    public function testCreateQueryPrePlugin()
    {
        $type = Client::QUERY_SELECT;
        $options = array('optionA' => 1, 'optionB' => 2);
        $expectedEvent = new PreCreateQueryEvent($type, $options);
        if (method_exists($expectedEvent, 'setDispatcher')) {
            $expectedEvent->setDispatcher($this->client->getEventDispatcher());
            $expectedEvent->setName(Events::PRE_CREATE_QUERY);
        }

        $observer = $this->getMock('Solarium\Core\Plugin\AbstractPlugin', array('preCreateQuery'));
        $observer->expects($this->once())
                 ->method('preCreateQuery')
                 ->with($this->equalTo($expectedEvent));

        $this->client->getEventDispatcher()->addListener(Events::PRE_CREATE_QUERY, array($observer, 'preCreateQuery'));
        $this->client->createQuery($type, $options);
    }

    public function testCreateQueryWithOverridingPlugin()
    {
        $type = Client::QUERY_SELECT;
        $options = array('query' => 'test789');
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

        $this->assertEquals(
            $expectedQuery,
            $returnedQuery
        );
    }

    public function testCreateQueryPostPlugin()
    {
        $type = Client::QUERY_SELECT;
        $options = array('optionA' => 1, 'optionB' => 2);
        $query = $this->client->createQuery($type, $options);
        $expectedEvent = new PostCreateQueryEvent($type, $options, $query);
        if (method_exists($expectedEvent, 'setDispatcher')) {
            $expectedEvent->setDispatcher($this->client->getEventDispatcher());
            $expectedEvent->setName(Events::POST_CREATE_QUERY);
        }

        $observer = $this->getMock('Solarium\Core\Plugin\AbstractPlugin', array('postCreateQuery'));
        $observer->expects($this->once())
                 ->method('postCreateQuery')
                 ->with($this->equalTo($expectedEvent));

        $this->client->getEventDispatcher()->addListener(
            Events::POST_CREATE_QUERY,
            array($observer, 'postCreateQuery')
        );

        $this->client->createQuery($type, $options);
    }

    public function testCreateSelect()
    {
        $options = array('optionA' => 1, 'optionB' => 2);

        $observer = $this->getMock('Solarium\Core\Client\Client', array('createQuery'));
        $observer->expects($this->once())
                 ->method('createQuery')
                 ->with($this->equalTo(Client::QUERY_SELECT), $this->equalTo($options));

        $observer->createSelect($options);
    }

    public function testCreateUpdate()
    {
        $options = array('optionA' => 1, 'optionB' => 2);

        $observer = $this->getMock('Solarium\Core\Client\Client', array('createQuery'));
        $observer->expects($this->once())
                 ->method('createQuery')
                 ->with($this->equalTo(Client::QUERY_UPDATE), $this->equalTo($options));

        $observer->createUpdate($options);
    }

    public function testCreatePing()
    {
        $options = array('optionA' => 1, 'optionB' => 2);

        $observer = $this->getMock('Solarium\Core\Client\Client', array('createQuery'));
        $observer->expects($this->once())
                 ->method('createQuery')
                 ->with($this->equalTo(Client::QUERY_PING), $this->equalTo($options));

        $observer->createPing($options);
    }

    public function testCreateMoreLikeThis()
    {
        $options = array('optionA' => 1, 'optionB' => 2);

        $observer = $this->getMock('Solarium\Core\Client\Client', array('createQuery'));
        $observer->expects($this->once())
                 ->method('createQuery')
                 ->with($this->equalTo(Client::QUERY_MORELIKETHIS), $this->equalTo($options));

        $observer->createMoreLikeThis($options);
    }

    public function testCreateAnalysisField()
    {
        $options = array('optionA' => 1, 'optionB' => 2);

        $observer = $this->getMock('Solarium\Core\Client\Client', array('createQuery'));
        $observer->expects($this->once())
                 ->method('createQuery')
                 ->with($this->equalTo(Client::QUERY_ANALYSIS_FIELD), $this->equalTo($options));

        $observer->createAnalysisField($options);
    }

    public function testCreateAnalysisDocument()
    {
        $options = array('optionA' => 1, 'optionB' => 2);

        $observer = $this->getMock('Solarium\Core\Client\Client', array('createQuery'));
        $observer->expects($this->once())
                 ->method('createQuery')
                 ->with($this->equalTo(Client::QUERY_ANALYSIS_DOCUMENT), $this->equalTo($options));

        $observer->createAnalysisDocument($options);
    }

    public function testCreateTerms()
    {
        $options = array('optionA' => 1, 'optionB' => 2);

        $observer = $this->getMock('Solarium\Core\Client\Client', array('createQuery'));
        $observer->expects($this->once())
                 ->method('createQuery')
                 ->with($this->equalTo(Client::QUERY_TERMS), $this->equalTo($options));

        $observer->createTerms($options);
    }

    public function testCreateSuggester()
    {
        $options = array('optionA' => 1, 'optionB' => 2);

        $observer = $this->getMock('Solarium\Core\Client\Client', array('createQuery'));
        $observer->expects($this->once())
                 ->method('createQuery')
                 ->with($this->equalTo(Client::QUERY_SUGGESTER), $this->equalTo($options));

        $observer->createSuggester($options);
    }

    public function testCreateExtract()
    {
        $options = array('optionA' => 1, 'optionB' => 2);

        $observer = $this->getMock('Solarium\Core\Client\Client', array('createQuery'));
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
        $response = new Response('{}', array('HTTP/1.1 200 OK'));

        return $response;
    }
}

class MyClientPlugin extends AbstractPlugin
{
}
