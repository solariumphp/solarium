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

    public function testGetAdapterWithDefaultAdapter()
    {

        $defaultAdapter = $this->_client->getOption('adapter');
        $adapter = $this->_client->getAdapter();
        $this->assertThat($adapter, $this->isInstanceOf($defaultAdapter));
    }

    public function testGetAdapterWithString()
    {
        $adapterClass = 'MyAdapter';
        $this->_client->setAdapter($adapterClass);
        $this->assertThat($this->_client->getAdapter(), $this->isInstanceOf($adapterClass));
    }
    
    public function testGetAdapterWithObject()
    {
        $adapterClass = 'MyAdapter';
        $this->_client->setAdapter(new $adapterClass);
        $this->assertThat($this->_client->getAdapter(), $this->isInstanceOf($adapterClass));
    }

    public function testRegisterQueryTypeAndGetQueryTypes()
    {
        $queryTypes = $this->_client->getQueryTypes();

        $this->_client->registerQueryType('myquerytype','mybuilder','myparser');

        $queryTypes['myquerytype'] = array(
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
        $this->_client->registerPlugin('testplugin','MyInvalidClientPlugin');
    }

    public function testGetInvalidPlugin()
    {
        $this->assertEquals(
            null,
            $this->_client->getPlugin('invalidplugin')
        );
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

        $this->_client->registerQueryType('testquerytype', $observer, '');
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

    }

    public function testCreateResultInvalidQueryType()
    {

    }

    public function testCreateResultPrePlugin()
    {

    }

    public function testCreateResultPostPlugin()
    {

    }

    public function testCreateResultWithOverridingPlugin()
    {

    }

    public function testPing()
    {

    }

    public function testSelect()
    {

    }

    public function testUpdate()
    {

    }

}

class MyAdapter extends Solarium_Client_Adapter_Http{

    public function execute($request)
    {
        $response = new Solarium_Client_Response('{}', array('HTTP/1.1 200 OK'));
        return $response;
    }
}

class myConfig{

    protected $_options;

    public function __construct($options)
    {
        $this->_options = $options;
    }

    public function toArray()
    {
        return $this->_options;
    }
}

class MyClientPlugin extends Solarium_Plugin_Abstract{

}

class MyInvalidClientPlugin{

}