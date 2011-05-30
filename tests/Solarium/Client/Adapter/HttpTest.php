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

class Solarium_Client_Adapter_HttpTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Solarium_Client_Adapter_Http
     */
    protected $_adapter;

    public function setUp()
    {
        $this->_adapter = new Solarium_Client_Adapter_Http();
    }

    public function testExecute()
    {
        $data = 'test123';

        $request = new Solarium_Client_Request();
        $request->setMethod(Solarium_Client_Request::METHOD_GET);

        $mock = $this->getMock('Solarium_Client_Adapter_Http', array('_getData','check'));
        $mock->expects($this->once())
             ->method('_getData')
             ->with($this->equalTo('http://127.0.0.1:8983/solr/?'), $this->isType('resource'))
             ->will($this->returnValue(array($data, array('HTTP 1.1 200 OK'))));

        $mock->execute($request);
    }

    public function testExecuteErrorResponse()
    {
        $data = 'test123';

        $request = new Solarium_Client_Request();

        $mock = $this->getMock('Solarium_Client_Adapter_Http', array('_getData','check'));
        $mock->expects($this->once())
             ->method('_getData')
             ->with($this->equalTo('http://127.0.0.1:8983/solr/?'), $this->isType('resource'))
             ->will($this->returnValue(array($data, array('HTTP 1.1 200 OK'))));
        $mock->expects($this->once())
             ->method('check')
             ->will($this->throwException(new Solarium_Client_HttpException("HTTP request failed")));

        $this->setExpectedException('Solarium_Client_HttpException');
        $mock->execute($request);
    }

    public function testCheckError()
    {
        $this->setExpectedException('Solarium_Client_HttpException');
        $this->_adapter->check(false, array());

    }

    public function testCheckOk()
    {
        $value = $this->_adapter->check('dummydata',array('HTTP 1.1 200 OK'));

        $this->assertEquals(
            null,
            $value
        );
    }

    public function testCreateContextGetRequest()
    {
        $timeout = 13;
        $method = Solarium_Client_Request::METHOD_HEAD;

        $request = new Solarium_Client_Request();
        $request->setMethod($method);
        $this->_adapter->setTimeout($timeout);

        $context = $this->_adapter->createContext($request);

        $this->assertEquals(
            array('http' => array('method' => $method, 'timeout' => $timeout)),
            stream_context_get_options($context)
        );
    }

    public function testCreateContextWithHeaders()
    {
        $timeout = 13;
        $method = Solarium_Client_Request::METHOD_HEAD;
        $header1 = 'Content-Type: text/xml; charset=UTF-8';
        $header2 = 'X-MyHeader: dummyvalue';

        $request = new Solarium_Client_Request();
        $request->setMethod($method);
        $request->addHeader($header1);
        $request->addHeader($header2);
        $this->_adapter->setTimeout($timeout);

        $context = $this->_adapter->createContext($request);

        $this->assertEquals(
            array('http' => array('method' => $method, 'timeout' => $timeout, 'header' => $header1."\r\n".$header2)),
            stream_context_get_options($context)
        );
    }

    public function testCreateContextPostRequest()
    {
        $timeout = 13;
        $method = Solarium_Client_Request::METHOD_POST;
        $data = 'test123';

        $request = new Solarium_Client_Request();
        $request->setMethod($method);
        $request->setRawData($data);
        $this->_adapter->setTimeout($timeout);

        $context = $this->_adapter->createContext($request);

        $this->assertEquals(
            array('http' => array('method' => $method, 'timeout' => $timeout, 'content' => $data, 'header' => 'Content-Type: text/xml; charset=UTF-8')),
            stream_context_get_options($context)
        );
    }

}