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

namespace Solarium\Tests\Core\Client\Adapter;

use Solarium\Core\Client\Adapter\ZendHttp as ZendHttpAdapter;
use Solarium\Core\Client\Request;
use Solarium\Core\Client\Endpoint;

class ZendHttpTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var ZendHttpAdapter
     */
    protected $adapter;

    public function setUp()
    {
        if (!class_exists('Zend_Loader_Autoloader') && !(@include_once 'Zend/Loader/Autoloader.php')) {
            $this->markTestSkipped('ZF not in include_path, skipping ZendHttp adapter tests');
        }

        \Zend_Loader_Autoloader::getInstance();

        $this->adapter = new ZendHttpAdapter();
    }

    public function testForwardingToZendHttpInSetOptions()
    {
        $options = array('optionZ' => 123, 'options' => array('optionX' => 'Y'));
        $adapterOptions = array('optionX' => 'Y');

        $mock = $this->getMock('Zend_Http_Client');
        $mock->expects($this->once())
                 ->method('setConfig')
                 ->with($this->equalTo($adapterOptions));

        $this->adapter->setZendHttp($mock);
        $this->adapter->setOptions($options);
    }

    public function testSetAndGetZendHttp()
    {
        $dummy = new \stdClass();

        $this->adapter->setZendHttp($dummy);

        $this->assertEquals(
            $dummy,
            $this->adapter->getZendHttp()
        );
    }

    public function testGetZendHttpAutoload()
    {
        $options = array('optionZ' => 123, 'options' => array('adapter' => 'Zend_Http_Client_Adapter_Curl'));
        $this->adapter->setOptions($options);

        $zendHttp = $this->adapter->getZendHttp();
        $this->assertThat($zendHttp, $this->isInstanceOf('Zend_Http_Client'));
    }

    public function testExecuteGet()
    {
        $method = Request::METHOD_GET;
        $rawData = 'xyz';
        $responseData = 'abc';
        $handler = 'myhandler';
        $headers = array(
            'X-test: 123'
        );
        $params = array('a' => 1, 'b' => 2);

        $request = new Request();
        $request->setMethod($method);
        $request->setHandler($handler);
        $request->setHeaders($headers);
        $request->setRawData($rawData);
        $request->setParams($params);

        $endpoint = new Endpoint();

        $response = new \Zend_Http_Response(200, array('status' => 'HTTP 1.1 200 OK'), $responseData);

        $mock = $this->getMock('Zend_Http_Client');
        $mock->expects($this->once())
                 ->method('setMethod')
                 ->with($this->equalTo($method));
        $mock->expects($this->once())
                 ->method('setUri')
                 ->with($this->equalTo('http://127.0.0.1:8983/solr/myhandler'));
        $mock->expects($this->once())
                 ->method('setHeaders')
                 ->with($this->equalTo(array('X-test: 123')));
        $mock->expects($this->once())
                 ->method('setParameterGet')
                 ->with($this->equalTo($params));
        $mock->expects($this->once())
                 ->method('request')
                 ->will($this->returnValue($response));

        $this->adapter->setZendHttp($mock);
        $adapterResponse = $this->adapter->execute($request, $endpoint);

        $this->assertEquals(
            $responseData,
            $adapterResponse->getBody()
        );
    }

    public function testExecutePost()
    {
        $method = Request::METHOD_POST;
        $rawData = 'xyz';
        $responseData = 'abc';
        $handler = 'myhandler';
        $headers = array(
            'X-test: 123'
        );
        $params = array('a' => 1, 'b' => 2);

        $request = new Request();
        $request->setMethod($method);
        $request->setHandler($handler);
        $request->setHeaders($headers);
        $request->setRawData($rawData);
        $request->setParams($params);

        $endpoint = new Endpoint();

        $response = new \Zend_Http_Response(200, array('status' => 'HTTP 1.1 200 OK'), $responseData);

        $mock = $this->getMock('Zend_Http_Client');
        $mock->expects($this->once())
                 ->method('setMethod')
                 ->with($this->equalTo($method));
        $mock->expects($this->once())
                 ->method('setUri')
                 ->with($this->equalTo('http://127.0.0.1:8983/solr/myhandler'));
        $mock->expects($this->once())
                 ->method('setHeaders')
                 ->with($this->equalTo(array('X-test: 123', 'Content-Type: text/xml; charset=UTF-8')));
        $mock->expects($this->once())
                 ->method('setRawData')
                 ->with($this->equalTo($rawData));
        $mock->expects($this->once())
                 ->method('setParameterGet')
                 ->with($this->equalTo($params));
        $mock->expects($this->once())
                 ->method('request')
                 ->will($this->returnValue($response));

        $this->adapter->setZendHttp($mock);
        $adapterResponse = $this->adapter->execute($request, $endpoint);

        $this->assertEquals(
            $responseData,
            $adapterResponse->getBody()
        );
    }

    public function testExecuteErrorResponse()
    {
        $request = new Request();
        $response = new \Zend_Http_Response(404, array(), '');
        $endpoint = new Endpoint();

        $mock = $this->getMock('Zend_Http_Client');
        $mock->expects($this->once())
                 ->method('request')
                 ->will($this->returnValue($response));

        $this->adapter->setZendHttp($mock);

        $this->setExpectedException('Solarium\Exception\HttpException');
        $this->adapter->execute($request, $endpoint);

    }

    public function testExecuteHeadRequestReturnsNoData()
    {
        $request = new Request();
        $request->setMethod(Request::METHOD_HEAD);
        $response = new \Zend_Http_Response(200, array('status' => 'HTTP 1.1 200 OK'), 'data');
        $endpoint = new Endpoint();

        $mock = $this->getMock('Zend_Http_Client');
        $mock->expects($this->once())
                 ->method('request')
                 ->will($this->returnValue($response));

        $this->adapter->setZendHttp($mock);
        $response = $this->adapter->execute($request, $endpoint);

        $this->assertEquals(
            '',
            $response->getBody()
        );
    }

    public function testExecuteWithInvalidMethod()
    {
        $request = new Request();
        $request->setMethod('invalid');
        $endpoint = new Endpoint();

        $this->setExpectedException('Solarium\Exception\OutOfBoundsException');
        $this->adapter->execute($request, $endpoint);
    }

    public function testExecuteWithFileUpload()
    {
        $request = new Request();
        $request->setMethod(Request::METHOD_POST);
        $request->setFileUpload(__FILE__);
        $endpoint = new Endpoint();
        $response = new \Zend_Http_Response(200, array('status' => 'HTTP 1.1 200 OK'), 'dummy');

        $mock = $this->getMock('Zend_Http_Client');
        $mock->expects($this->once())
             ->method('setFileUpload')
             ->with(
                 $this->equalTo('content'),
                 $this->equalTo('content'),
                 $this->equalTo(file_get_contents(__FILE__)),
                 $this->equalTo('application/octet-stream; charset=binary')
             );
        $mock->expects($this->once())
             ->method('request')
             ->will($this->returnValue($response));

        $this->adapter->setZendHttp($mock);
        $this->adapter->execute($request, $endpoint);
    }
}
