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

use Solarium\Core\Client\Adapter\Http as HttpAdapter;
use Solarium\Core\Client\Request;
use Solarium\Core\Client\Endpoint;
use Solarium\Exception\HttpException;

class HttpTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var HttpAdapter
     */
    protected $adapter;

    public function setUp()
    {
        $this->adapter = new HttpAdapter();
    }

    public function testExecute()
    {
        $data = 'test123';

        $request = new Request();
        $request->setMethod(Request::METHOD_GET);

        $endpoint = new Endpoint();

        $mock = $this->getMock('Solarium\Core\Client\Adapter\Http', array('getData', 'check'));
        $mock->expects($this->once())
             ->method('getData')
             ->with($this->equalTo('http://127.0.0.1:8983/solr/?'), $this->isType('resource'))
             ->will($this->returnValue(array($data, array('HTTP 1.1 200 OK'))));

        $mock->execute($request, $endpoint);
    }

    public function testExecuteErrorResponse()
    {
        $data = 'test123';

        $request = new Request();
        $endpoint = new Endpoint();

        $mock = $this->getMock('Solarium\Core\Client\Adapter\Http', array('getData', 'check'));
        $mock->expects($this->once())
             ->method('getData')
             ->with($this->equalTo('http://127.0.0.1:8983/solr/?'), $this->isType('resource'))
             ->will($this->returnValue(array($data, array('HTTP 1.1 200 OK'))));
        $mock->expects($this->once())
             ->method('check')
             ->will($this->throwException(new HttpException("HTTP request failed")));

        $this->setExpectedException('Solarium\Exception\HttpException');
        $mock->execute($request, $endpoint);
    }

    public function testCheckError()
    {
        $this->setExpectedException('Solarium\Exception\HttpException');
        $this->adapter->check(false, array());

    }

    public function testCheckOk()
    {
        $value = $this->adapter->check('dummydata', array('HTTP 1.1 200 OK'));

        $this->assertEquals(
            null,
            $value
        );
    }

    public function testCreateContextGetRequest()
    {
        $timeout = 13;
        $method = Request::METHOD_HEAD;

        $request = new Request();
        $request->setMethod($method);
        $endpoint = new Endpoint();
        $endpoint->setTimeout($timeout);

        $context = $this->adapter->createContext($request, $endpoint);

        $this->assertEquals(
            array('http' => array('method' => $method, 'timeout' => $timeout)),
            stream_context_get_options($context)
        );
    }

    public function testCreateContextWithHeaders()
    {
        $timeout = 13;
        $method = Request::METHOD_HEAD;
        $header1 = 'Content-Type: text/xml; charset=UTF-8';
        $header2 = 'X-MyHeader: dummyvalue';

        $request = new Request();
        $request->setMethod($method);
        $request->addHeader($header1);
        $request->addHeader($header2);
        $endpoint = new Endpoint();
        $endpoint->setTimeout($timeout);

        $context = $this->adapter->createContext($request, $endpoint);

        $this->assertEquals(
            array('http' => array('method' => $method, 'timeout' => $timeout, 'header' => $header1."\r\n".$header2)),
            stream_context_get_options($context)
        );
    }

    public function testCreateContextPostRequest()
    {
        $timeout = 13;
        $method = Request::METHOD_POST;
        $data = 'test123';

        $request = new Request();
        $request->setMethod($method);
        $request->setRawData($data);
        $endpoint = new Endpoint();
        $endpoint->setTimeout($timeout);

        $context = $this->adapter->createContext($request, $endpoint);

        $this->assertEquals(
            array(
                'http' => array(
                    'method' => $method,
                    'timeout' => $timeout,
                    'content' => $data,
                    'header' => 'Content-Type: text/xml; charset=UTF-8',
                )
            ),
            stream_context_get_options($context)
        );
    }

    public function testCreateContextPostFileRequest()
    {
        $timeout = 13;
        $method = Request::METHOD_POST;
        $data = 'test123';

        $request = new Request();
        $request->setMethod($method);
        $request->setFileUpload(__FILE__);
        $endpoint = new Endpoint();
        $endpoint->setTimeout($timeout);

        $context = $this->adapter->createContext($request, $endpoint);

        $this->assertEquals(
            array(
                'http' => array(
                    'method' => $method,
                    'timeout' => $timeout,
                    'content' => file_get_contents(__FILE__),
                    'header' => 'Content-Type: multipart/form-data',
                )
            ),
            stream_context_get_options($context)
        );
    }

    public function testCreateContextWithAuthorization()
    {
        $timeout = 13;
        $method = Request::METHOD_HEAD;

        $request = new Request();
        $request->setMethod($method);
        $request->setAuthentication('someone', 'S0M3p455');

        $endpoint = new Endpoint();
        $endpoint->setTimeout($timeout);

        $context = $this->adapter->createContext($request, $endpoint);

        $this->assertEquals(
            array(
                'http' => array(
                    'method' => $method,
                    'timeout' => $timeout,
                    'header' => 'Authorization: Basic c29tZW9uZTpTME0zcDQ1NQ==',
                )
            ),
            stream_context_get_options($context)
        );
    }
}
