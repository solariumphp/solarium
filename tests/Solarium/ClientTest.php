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

class Solarium_ClientTest extends PHPUnit_Framework_TestCase
{

    /**
     * @var Solarium_Client
     */
    protected $_client;

    public function setUp()
    {
        $this->_client = new Solarium_Client();
    }

    public function testConfigMode()
    {
        $options = array(
            'adapter' => 'MyAdapter',
            'adapteroptions' => array(
                'host' => 'myhost',
                'port' => 8080,
            ),
            'querytype' => array(
                'myquerytype' => array(
                    'query'          => 'MyQuery',
                    'requestbuilder' => 'MyRequestBuilder',
                    'responseparser' => 'MyResponseParser'
                )
            ),
            'plugin' => array(
                'myplugin' => array(
                    'plugin' => 'MyClientPlugin',
                    'options' => array(
                        'option1' => 'value1',
                        'option2' => 'value2',
                    )
                )
            ),
        );

        $this->_client->setOptions($options);

        $adapter = $this->_client->getAdapter();

        $this->assertThat($adapter, $this->isInstanceOf('MyAdapter'));
        $this->assertEquals(8080, $adapter->getPort());


        $queryTypes = $this->_client->getQueryTypes();
        $this->assertEquals(
            $options['querytype']['myquerytype'],
            $queryTypes['myquerytype']
        );

        $plugin = $this->_client->getPlugin('myplugin');
        $this->assertThat($plugin, $this->isInstanceOf('MyClientPlugin'));
        $this->assertEquals($options['plugin']['myplugin']['options'], $plugin->getOptions());

    }

    public function testConfigModeWithoutKeys()
    {
        $options = array(
            'adapter' => 'MyAdapter',
            'adapteroptions' => array(
                'host' => 'myhost',
                'port' => 8080,
            ),
            'querytype' => array(
                array(
                    'type'           => 'myquerytype',
                    'query'          => 'MyQuery',
                    'requestbuilder' => 'MyRequestBuilder',
                    'responseparser' => 'MyResponseParser',
                )
            ),
            'plugin' => array(
                 array(
                    'key'     => 'myplugin',
                    'plugin'  => 'MyClientPlugin',
                    'options' => array(
                        'option1' => 'value1',
                        'option2' => 'value2',
                    )
                )
            ),
        );

        $this->_client->setOptions($options);

        $adapter = $this->_client->getAdapter();

        $this->assertThat($adapter, $this->isInstanceOf('MyAdapter'));
        $this->assertEquals(8080, $adapter->getPort());

        $queryTypes = $this->_client->getQueryTypes();
        $this->assertEquals(
            array(
                'requestbuilder' => 'MyRequestBuilder',
                'responseparser' => 'MyResponseParser',
                'query'          => 'MyQuery',
            ),
            $queryTypes['myquerytype']
        );

        $plugin = $this->_client->getPlugin('myplugin');
        $this->assertThat($plugin, $this->isInstanceOf('MyClientPlugin'));
        $this->assertEquals($options['plugin'][0]['options'], $plugin->getOptions());
    }

    public function testSetAndGetAdapterWithDefaultAdapter()
    {
        $defaultAdapter = $this->_client->getOption('adapter');
        $adapter = $this->_client->getAdapter();
        $this->assertThat($adapter, $this->isInstanceOf($defaultAdapter));
    }

    public function testSetAndGetAdapterWithString()
    {
        $adapterClass = 'MyAdapter';
        $this->_client->setAdapter($adapterClass);
        $this->assertThat($this->_client->getAdapter(), $this->isInstanceOf($adapterClass));
    }

    public function testSetAndGetAdapterWithObject()
    {
        $adapterClass = 'MyAdapter';
        $this->_client->setAdapter(new $adapterClass);
        $this->assertThat($this->_client->getAdapter(), $this->isInstanceOf($adapterClass));
    }

    public function testRegisterQueryTypeAndGetQueryTypes()
    {
        $queryTypes = $this->_client->getQueryTypes();

        $this->_client->registerQueryType('myquerytype','myquery','mybuilder','myparser');

        $queryTypes['myquerytype'] = array(
            'query' => 'myquery',
            'requestbuilder' => 'mybuilder',
            'responseparser' => 'myparser',
        );

        $this->assertEquals(
            $queryTypes,
            $this->_client->getQueryTypes()
        );
    }

    public function testRegisterAndGetPlugin()
    {
        $options = array('option1' => 1);
        $this->_client->registerPlugin('testplugin','MyClientPlugin',$options);

        $plugin = $this->_client->getPlugin('testplugin');

        $this->assertThat(
            $plugin,
            $this->isInstanceOf('MyClientPlugin')
        );

        $this->assertEquals(
            $options,
            $plugin->getOptions()
        );
    }

    public function testRegisterInvalidPlugin()
    {
        $this->setExpectedException('Solarium_Exception');
        $this->_client->registerPlugin('testplugin','StdClass');
    }

    public function testGetInvalidPlugin()
    {
        $this->assertEquals(
            null,
            $this->_client->getPlugin('invalidplugin', false)
        );
    }

    public function testAutoloadPlugin()
    {
        $loadbalancer = $this->_client->getPlugin('loadbalancer');
        $this->assertThat(
            $loadbalancer,
            $this->isInstanceOf('Solarium_Plugin_Loadbalancer')
        );
    }

    public function testAutoloadInvalidPlugin()
    {
        $this->setExpectedException('Solarium_Exception');
        $this->_client->getPlugin('invalidpluginname');
    }

    public function testRemoveAndGetPlugins()
    {
        $options = array('option1' => 1);
        $this->_client->registerPlugin('testplugin','MyClientPlugin',$options);

        $plugin = $this->_client->getPlugin('testplugin');
        $plugins = $this->_client->getPlugins();

        $this->assertEquals(
            array('testplugin' => $plugin),
            $plugins
        );

        $this->_client->removePlugin('testplugin');
        $plugins = $this->_client->getPlugins();

        $this->assertEquals(
            array(),
            $plugins
        );
    }

    public function testRemovePluginAndGetPluginsWithObjectInput()
    {
        $options = array('option1' => 1);
        $this->_client->registerPlugin('testplugin','MyClientPlugin',$options);

        $plugin = $this->_client->getPlugin('testplugin');
        $plugins = $this->_client->getPlugins();

        $this->assertEquals(
            array('testplugin' => $plugin),
            $plugins
        );

        $this->_client->removePlugin($plugin);
        $plugins = $this->_client->getPlugins();

        $this->assertEquals(
            array(),
            $plugins
        );
    }

    public function testCreateRequest()
    {
        $queryStub = $this->getMock('Solarium_Query_Select');
        $queryStub->expects($this->any())
             ->method('getType')
             ->will($this->returnValue('testquerytype'));

        $observer = $this->getMock('Solarium_Client_RequestBuilder', array('build'));
        $observer->expects($this->once())
                 ->method('build')
                 ->with($this->equalTo($queryStub));

        $this->_client->registerQueryType('testquerytype', 'Solarium_Query_Select', $observer, '');
        $this->_client->createRequest($queryStub);
    }

    public function testCreateRequestInvalidQueryType()
    {
        $queryStub = $this->getMock('Solarium_Query_Select');
        $queryStub->expects($this->any())
             ->method('getType')
             ->will($this->returnValue('testquerytype'));

        $this->setExpectedException('Solarium_Exception');
        $this->_client->createRequest($queryStub);
    }

    public function testCreateRequestPrePlugin()
    {
        $query = new Solarium_Query_Select();

        $observer = $this->getMock('Solarium_Plugin_Abstract', array(), array($this->_client,array()));
        $observer->expects($this->once())
                 ->method('preCreateRequest')
                 ->with($this->equalTo($query));

        $this->_client->registerPlugin('testplugin', $observer);
        $this->_client->createRequest($query);
    }

    public function testCreateRequestPostPlugin()
    {
        $query = new Solarium_Query_Select();
        $request = $this->_client->createRequest($query);

        $observer = $this->getMock('Solarium_Plugin_Abstract', array(), array($this->_client,array()));
        $observer->expects($this->once())
                 ->method('postCreateRequest')
                 ->with($this->equalTo($query),$this->equalTo($request));

        $this->_client->registerPlugin('testplugin', $observer);
        $this->_client->createRequest($query);
    }

    public function testCreateRequestWithOverridingPlugin()
    {
        $overrideValue =  'dummyvalue';
        $query = new Solarium_Query_Select();

        $observer = $this->getMock('Solarium_Plugin_Abstract', array(), array($this->_client,array()));
        $observer->expects($this->once())
                 ->method('preCreateRequest')
                 ->with($this->equalTo($query))
                 ->will($this->returnValue($overrideValue));

        $this->_client->registerPlugin('testplugin', $observer);
        $request = $this->_client->createRequest($query);

        $this->assertEquals(
            $overrideValue,
            $request
        );
    }

    public function testCreateResult()
    {
        $query = new Solarium_Query_Select();
        $response = new Solarium_Client_Response('',array('HTTP 1.0 200 OK'));
        $result = $this->_client->createResult($query, $response);

        $this->assertThat(
            $result,
            $this->isInstanceOf($query->getResultClass())
        );
    }

    public function testCreateResultPrePlugin()
    {
        $query = new Solarium_Query_Select();
        $response = new Solarium_Client_Response('',array('HTTP 1.0 200 OK'));

        $observer = $this->getMock('Solarium_Plugin_Abstract', array(), array($this->_client,array()));
        $observer->expects($this->once())
                 ->method('preCreateResult')
                 ->with($this->equalTo($query),$this->equalTo($response));

        $this->_client->registerPlugin('testplugin', $observer);
        $this->_client->createResult($query, $response);
    }

    public function testCreateResultPostPlugin()
    {
        $query = new Solarium_Query_Select();
        $response = new Solarium_Client_Response('',array('HTTP 1.0 200 OK'));
        $result = $this->_client->createResult($query, $response);

        $observer = $this->getMock('Solarium_Plugin_Abstract', array(), array($this->_client,array()));
        $observer->expects($this->once())
                 ->method('postCreateResult')
                 ->with($this->equalTo($query), $this->equalTo($response), $this->equalTo($result));

        $this->_client->registerPlugin('testplugin', $observer);
        $this->_client->createResult($query, $response);
    }

    public function testCreateResultWithOverridingPlugin()
    {
        $overrideValue =  'dummyvalue';
        $query = new Solarium_Query_Select();
        $response = new Solarium_Client_Response('',array('HTTP 1.0 200 OK'));

        $observer = $this->getMock('Solarium_Plugin_Abstract', array(), array($this->_client,array()));
        $observer->expects($this->once())
                 ->method('preCreateResult')
                 ->with($this->equalTo($query), $this->equalTo($response))
                 ->will($this->returnValue($overrideValue));

        $this->_client->registerPlugin('testplugin', $observer);
        $result = $this->_client->createResult($query, $response);

        $this->assertEquals(
            $overrideValue,
            $result
        );
    }

    public function testExecute()
    {
        $query = new Solarium_Query_Ping();

        $observer = $this->getMock('Solarium_Client', array('createRequest','executeRequest','createResult'));

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
                 ->with($this->equalTo($query),$this->equalTo('dummyresponse'));

        $observer->execute($query);
    }

    public function testExecutePrePlugin()
    {
        $query = new Solarium_Query_Ping();

        $mock = $this->getMock('Solarium_Client', array('createRequest','executeRequest','createResult'));

        $mock->expects($this->once())
             ->method('createRequest')
             ->will($this->returnValue('dummyrequest'));

        $mock->expects($this->once())
             ->method('executeRequest')
             ->will($this->returnValue('dummyresponse'));

        $mock->expects($this->once())
             ->method('createResult')
             ->will($this->returnValue('dummyresult'));

        $observer = $this->getMock('Solarium_Plugin_Abstract', array(), array($this->_client,array()));
        $observer->expects($this->once())
                 ->method('preExecute')
                 ->with($this->equalTo($query));

        $mock->registerPlugin('testplugin', $observer);
        $mock->execute($query);
    }

    public function testExecutePostPlugin()
    {
        $query = new Solarium_Query_Ping();

        $mock = $this->getMock('Solarium_Client', array('createRequest','executeRequest','createResult'));

        $mock->expects($this->once())
             ->method('createRequest')
             ->will($this->returnValue('dummyrequest'));

        $mock->expects($this->once())
             ->method('executeRequest')
             ->will($this->returnValue('dummyresponse'));

        $mock->expects($this->once())
             ->method('createResult')
             ->will($this->returnValue('dummyresult'));

        $observer = $this->getMock('Solarium_Plugin_Abstract', array(), array($this->_client,array()));
        $observer->expects($this->once())
                 ->method('postExecute')
                 ->with($this->equalTo($query), $this->equalTo('dummyresult'));

        $mock->registerPlugin('testplugin', $observer);
        $mock->execute($query);
    }

    public function testExecuteWithOverridingPlugin()
    {
        $query = new Solarium_Query_Ping();

        $observer = $this->getMock('Solarium_Plugin_Abstract', array(), array($this->_client,array()));
        $observer->expects($this->once())
                 ->method('preExecute')
                 ->with($this->equalTo($query))
                 ->will($this->returnValue('dummyoverride'));

        $this->_client->registerPlugin('testplugin', $observer);
        $result = $this->_client->execute($query);

        $this->assertEquals(
            'dummyoverride',
            $result
        );
    }

    public function testExecuteRequest()
    {
        $request = new Solarium_Client_Request();
        $dummyResponse = 'dummyresponse';

        $observer = $this->getMock('Solarium_Client_Adapter', array('execute'));
        $observer->expects($this->once())
                 ->method('execute')
                 ->with($this->equalTo($request))
                 ->will($this->returnValue($dummyResponse));

        $this->_client->setAdapter($observer);
        $response = $this->_client->executeRequest($request);

        $this->assertEquals(
            $dummyResponse,
            $response
        );
    }

    public function testExecuteRequestPrePlugin()
    {
        $request = new Solarium_Client_Request();
        $dummyResponse = 'dummyresponse';

        $mockAdapter = $this->getMock('Solarium_Client_Adapter', array('execute'));
        $mockAdapter->expects($this->once())
                 ->method('execute')
                 ->with($this->equalTo($request))
                 ->will($this->returnValue($dummyResponse));
        $this->_client->setAdapter($mockAdapter);

        $observer = $this->getMock('Solarium_Plugin_Abstract', array(), array($this->_client,array()));
        $observer->expects($this->once())
                 ->method('preExecuteRequest')
                 ->with($this->equalTo($request));

        $this->_client->registerPlugin('testplugin', $observer);
        $this->_client->executeRequest($request);
    }

    public function testExecuteRequestPostPlugin()
    {
        $request = new Solarium_Client_Request();
        $dummyResponse = 'dummyresponse';

        $mockAdapter = $this->getMock('Solarium_Client_Adapter', array('execute'));
        $mockAdapter->expects($this->any())
                 ->method('execute')
                 ->with($this->equalTo($request))
                 ->will($this->returnValue($dummyResponse));
        $this->_client->setAdapter($mockAdapter);

        $response = $this->_client->executeRequest($request);

        $observer = $this->getMock('Solarium_Plugin_Abstract', array(), array($this->_client,array()));
        $observer->expects($this->once())
                 ->method('postExecuteRequest')
                 ->with($this->equalTo($request), $this->equalTo($response));

        $this->_client->registerPlugin('testplugin', $observer);
        $this->_client->executeRequest($request);
    }

    public function testExecuteRequestWithOverridingPlugin()
    {
        $request = new Solarium_Client_Request();
        $dummyOverride = 'dummyoverride';

        $observer = $this->getMock('Solarium_Plugin_Abstract', array(), array($this->_client,array()));
        $observer->expects($this->once())
                 ->method('preExecuteRequest')
                 ->with($this->equalTo($request))
                    ->will($this->returnValue($dummyOverride));

        $this->_client->registerPlugin('testplugin', $observer);
        $response = $this->_client->executeRequest($request);

        $this->assertEquals(
            $dummyOverride,
            $response
        );
    }

    public function testPing()
    {
        $query = new Solarium_Query_Ping();

        $observer = $this->getMock('Solarium_Client', array('execute'));
        $observer->expects($this->once())
                 ->method('execute')
                 ->with($this->equalTo($query));

        $observer->ping($query);
    }

    public function testSelect()
    {
        $query = new Solarium_Query_Select();

        $observer = $this->getMock('Solarium_Client', array('execute'));
        $observer->expects($this->once())
                 ->method('execute')
                 ->with($this->equalTo($query));

        $observer->select($query);
    }

    public function testUpdate()
    {
        $query = new Solarium_Query_Update();

        $observer = $this->getMock('Solarium_Client', array('execute'));
        $observer->expects($this->once())
                 ->method('execute')
                 ->with($this->equalTo($query));

        $observer->update($query);
    }

    public function testMoreLikeThis()
    {
        $query = new Solarium_Query_MoreLikeThis();

        $observer = $this->getMock('Solarium_Client', array('execute'));
        $observer->expects($this->once())
                 ->method('execute')
                 ->with($this->equalTo($query));

        $observer->moreLikeThis($query);
    }

    public function testAnalyze()
    {
        $query = new Solarium_Query_Analysis_Field();

        $observer = $this->getMock('Solarium_Client', array('execute'));
        $observer->expects($this->once())
                 ->method('execute')
                 ->with($this->equalTo($query));

        $observer->analyze($query);
    }

    public function testTerms()
    {
        $query = new Solarium_Query_Terms();

        $observer = $this->getMock('Solarium_Client', array('execute'));
        $observer->expects($this->once())
                 ->method('execute')
                 ->with($this->equalTo($query));

        $observer->terms($query);
    }

    public function testSuggester()
    {
        $query = new Solarium_Query_Suggester();

        $observer = $this->getMock('Solarium_Client', array('execute'));
        $observer->expects($this->once())
                 ->method('execute')
                 ->with($this->equalTo($query));

        $observer->suggester($query);
    }

    public function testCreateQuery()
    {
        $options = array('optionA' => 1, 'optionB' => 2);
        $query = $this->_client->createQuery(Solarium_Client::QUERYTYPE_SELECT, $options);

        // check class mapping
        $this->assertThat($query, $this->isInstanceOf('Solarium_Query_Select'));

        // check option forwarding
        $queryOptions = $query->getOptions();
        $this->assertEquals(
            $options['optionB'],
            $queryOptions['optionB']
        );
    }

    public function testCreateQueryWithInvalidQueryType()
    {
        $this->setExpectedException('Solarium_Exception');
        $this->_client->createQuery('invalidtype');
    }

    public function testCreateQueryPrePlugin()
    {
        $type = Solarium_Client::QUERYTYPE_SELECT;
        $options = array('optionA' => 1, 'optionB' => 2);

        $observer = $this->getMock('Solarium_Plugin_Abstract', array(), array($this->_client,array()));
        $observer->expects($this->once())
                 ->method('preCreateQuery')
                 ->with($this->equalTo($type), $this->equalTo($options));

        $this->_client->registerPlugin('testplugin', $observer);
        $this->_client->createQuery($type, $options);
    }

    public function testCreateQueryWithOverridingPlugin()
    {
        $type = Solarium_Client::QUERYTYPE_SELECT;
        $options = array('optionA' => 1, 'optionB' => 2);
        $dummyvalue = 'test123';

        $observer = $this->getMock('Solarium_Plugin_Abstract', array(), array($this->_client,array()));
        $observer->expects($this->once())
                 ->method('preCreateQuery')
                 ->with($this->equalTo($type), $this->equalTo($options))
                 ->will($this->returnValue($dummyvalue));

        $this->_client->registerPlugin('testplugin', $observer);
        $query = $this->_client->createQuery($type, $options);

        $this->assertEquals(
            $dummyvalue,
            $query
        );
    }

    public function testCreateQueryPostPlugin()
    {
        $type = Solarium_Client::QUERYTYPE_SELECT;
        $options = array('optionA' => 1, 'optionB' => 2);
        $query = $this->_client->createQuery($type, $options);

        $observer = $this->getMock('Solarium_Plugin_Abstract', array(), array($this->_client,array()));
        $observer->expects($this->once())
                 ->method('postCreateQuery')
                 ->with($this->equalTo($type), $this->equalTo($options), $this->equalTo($query));

        $this->_client->registerPlugin('testplugin', $observer);
        $this->_client->createQuery($type, $options);
    }

    public function testCreateSelect()
    {
        $options = array('optionA' => 1, 'optionB' => 2);

        $observer = $this->getMock('Solarium_Client', array('createQuery'));
        $observer->expects($this->once())
                 ->method('createQuery')
                 ->with($this->equalTo(Solarium_Client::QUERYTYPE_SELECT), $this->equalTo($options));

        $observer->createSelect($options);
    }

    public function testCreateUpdate()
    {
        $options = array('optionA' => 1, 'optionB' => 2);

        $observer = $this->getMock('Solarium_Client', array('createQuery'));
        $observer->expects($this->once())
                 ->method('createQuery')
                 ->with($this->equalTo(Solarium_Client::QUERYTYPE_UPDATE), $this->equalTo($options));

        $observer->createUpdate($options);
    }

    public function testCreatePing()
    {
        $options = array('optionA' => 1, 'optionB' => 2);

        $observer = $this->getMock('Solarium_Client', array('createQuery'));
        $observer->expects($this->once())
                 ->method('createQuery')
                 ->with($this->equalTo(Solarium_Client::QUERYTYPE_PING), $this->equalTo($options));

        $observer->createPing($options);
    }

    public function testCreateMoreLikeThis()
    {
        $options = array('optionA' => 1, 'optionB' => 2);

        $observer = $this->getMock('Solarium_Client', array('createQuery'));
        $observer->expects($this->once())
                 ->method('createQuery')
                 ->with($this->equalTo(Solarium_Client::QUERYTYPE_MORELIKETHIS), $this->equalTo($options));

        $observer->createMoreLikeThis($options);
    }

    public function testCreateAnalysisField()
    {
        $options = array('optionA' => 1, 'optionB' => 2);

        $observer = $this->getMock('Solarium_Client', array('createQuery'));
        $observer->expects($this->once())
                 ->method('createQuery')
                 ->with($this->equalTo(Solarium_Client::QUERYTYPE_ANALYSIS_FIELD), $this->equalTo($options));

        $observer->createAnalysisField($options);
    }

    public function testCreateAnalysisDocument()
    {
        $options = array('optionA' => 1, 'optionB' => 2);

        $observer = $this->getMock('Solarium_Client', array('createQuery'));
        $observer->expects($this->once())
                 ->method('createQuery')
                 ->with($this->equalTo(Solarium_Client::QUERYTYPE_ANALYSIS_DOCUMENT), $this->equalTo($options));

        $observer->createAnalysisDocument($options);
    }

    public function testCreateTerms()
    {
        $options = array('optionA' => 1, 'optionB' => 2);

        $observer = $this->getMock('Solarium_Client', array('createQuery'));
        $observer->expects($this->once())
                 ->method('createQuery')
                 ->with($this->equalTo(Solarium_Client::QUERYTYPE_TERMS), $this->equalTo($options));

        $observer->createTerms($options);
    }

    public function testCreateSuggester()
    {
        $options = array('optionA' => 1, 'optionB' => 2);

        $observer = $this->getMock('Solarium_Client', array('createQuery'));
        $observer->expects($this->once())
                 ->method('createQuery')
                 ->with($this->equalTo(Solarium_Client::QUERYTYPE_SUGGESTER), $this->equalTo($options));

        $observer->createSuggester($options);
    }

    public function testTriggerEvent()
    {
        $eventName = 'Test';
        $params = array('a', 'b');
        $override = true;

        $clientMock = $this->getMock('Solarium_Client', array('_callPlugins'));
        $clientMock->expects($this->once())
             ->method('_callPlugins')
             ->with($this->equalTo('event'.$eventName), $this->equalTo($params), $override);

        $clientMock->triggerEvent($eventName, $params, $override);
    }

}

class MyAdapter extends Solarium_Client_Adapter_Http{

    public function execute($request)
    {
        $response = new Solarium_Client_Response('{}', array('HTTP/1.1 200 OK'));
        return $response;
    }
}

class MyClientPlugin extends Solarium_Plugin_Abstract{

}