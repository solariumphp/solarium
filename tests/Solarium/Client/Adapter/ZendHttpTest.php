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

class Solarium_Client_Adapter_ZendHttpTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Solarium_Client_Adapter_ZendHttp
     */
    protected $_adapter;

    public function setUp()
    {
        if (!class_exists('Zend_Loader_Autoloader') && (@include_once 'Zend/Loader/Autoloader.php') !== 'OK') {
            $this->markTestSkipped('ZF not in include_path, skipping ZendHttp adapter tests');
        }

        Zend_Loader_Autoloader::getInstance();

        $this->_adapter = new Solarium_Client_Adapter_ZendHttp();
    }

    public function testForwardingToZendHttpInSetOptions()
    {
        $options = array('timeout' => 10, 'optionZ' => 123, 'options' => array('optionX' => 'Y'));
        $adapterOptions = array('timeout' => 10, 'optionX' => 'Y');
        
        $mock = $this->getMock('Zend_Http_Client');
        $mock->expects($this->once())
                 ->method('setConfig')
                 ->with($this->equalTo($adapterOptions));

        $this->_adapter->setZendHttp($mock);
        $this->_adapter->setOptions($options);
    }

    public function testSetAndGetZendHttp()
    {
        $dummy = new StdClass();

        $this->_adapter->setZendHttp($dummy);

        $this->assertEquals(
            $dummy,
            $this->_adapter->getZendHttp()
        );
    }

    public function testGetZendHttpAutoload()
    {
        $options = array('timeout' => 10, 'optionZ' => 123, 'options' => array('adapter' => 'Zend_Http_Client_Adapter_Curl'));
        $this->_adapter->setOptions($options);

        $zendHttp = $this->_adapter->getZendHttp();
        $this->assertThat($zendHttp, $this->isInstanceOf('Zend_Http_Client'));
    }

    public function testExecute()
    {
        $method = Solarium_Client_Request::METHOD_GET;
        $rawData = 'xyz';
        $responseData = 'abc';
        $handler = 'myhandler';
        $headers = array(
            'Content-Type: application/x-www-form-urlencoded'
        );

        $request = new Solarium_Client_Request();
        $request->setMethod($method);
        $request->setHandler($handler);
        $request->setHeaders($headers);
        $request->setRawData($rawData);

        $response = new Zend_Http_Response(200, array('status' => 'HTTP 1.1 200 OK'), $responseData);

        $mock = $this->getMock('Zend_Http_Client');
        $mock->expects($this->once())
                 ->method('setMethod')
                 ->with($this->equalTo($method));
        $mock->expects($this->once())
                 ->method('setUri')
                 ->with($this->equalTo('http://127.0.0.1:8983/solr/myhandler?'));
        $mock->expects($this->once())
                 ->method('setHeaders')
                 ->with($this->equalTo($headers));
        $mock->expects($this->once())
                 ->method('setRawData')
                 ->with($this->equalTo($rawData));
        $mock->expects($this->once())
                 ->method('request')
                 ->will($this->returnValue($response));

        $this->_adapter->setZendHttp($mock);
        $adapterResponse = $this->_adapter->execute($request);

        $this->assertEquals(
            $responseData,
            $adapterResponse->getBody()
        );
    }

    public function testExecuteErrorResponse()
    {
        $request = new Solarium_Client_Request();
        $response = new Zend_Http_Response(404, array(), '');

        $mock = $this->getMock('Zend_Http_Client');
        $mock->expects($this->once())
                 ->method('request')
                 ->will($this->returnValue($response));

        $this->_adapter->setZendHttp($mock);

        $this->setExpectedException('Solarium_Client_HttpException');
        $this->_adapter->execute($request);

    }

    public function testExecuteHeadRequestReturnsNoData()
    {
        $request = new Solarium_Client_Request();
        $request->setMethod(Solarium_Client_Request::METHOD_HEAD);
        $response = new Zend_Http_Response(200, array('status' => 'HTTP 1.1 200 OK'), 'data');

        $mock = $this->getMock('Zend_Http_Client');
        $mock->expects($this->once())
                 ->method('request')
                 ->will($this->returnValue($response));

        $this->_adapter->setZendHttp($mock);
        $response = $this->_adapter->execute($request);

        $this->assertEquals(
            '',
            $response->getBody()
        );
    }

}