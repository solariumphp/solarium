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

use Solarium\Core\Client\Adapter\PeclHttp as PeclHttpAdapter;
use Solarium\Core\Client\Request;
use Solarium\Core\Client\Endpoint;
use Solarium\Exception\ExceptionInterface;

class PeclHttpTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var PeclHttpAdapter
     */
    protected $adapter;

    public function setUp()
    {
        if (!function_exists('http_get')) {
            $this->markTestSkipped('Pecl_http not available, skipping PeclHttp adapter tests');
        }

        $this->adapter = new PeclHttpAdapter(array('timeout' => 10));
    }

    /**
     * @dataProvider requestProvider
     */
    public function testToHttpRequestWithMethod($request, $method, $support)
    {
        $endpoint = new Endpoint();

        try {
            $httpRequest = $this->adapter->toHttpRequest($request, $endpoint);
            $this->assertEquals($httpRequest->getMethod(), $method);
        } catch (ExceptionInterface $e) {
            if ($support) {
                $this->fail("Unsupport method: {$request->getMethod()}");
            }
        }
    }

    public function requestProvider()
    {
        // prevents undefined constants errors
        if (function_exists('http_get')) {
            $methods = array(
                Request::METHOD_GET => array(
                    'method' => HTTP_METH_GET,
                    'support' => true,
                ),
                Request::METHOD_POST => array(
                    'method' => HTTP_METH_POST,
                    'support' => true,
                ),
                Request::METHOD_HEAD => array(
                    'method' => HTTP_METH_HEAD,
                    'support' => true,
                ),
                'PUT' => array(
                    'method' => HTTP_METH_PUT,
                    'support' => false,
                ),
                'DELETE' => array(
                    'method' => HTTP_METH_DELETE,
                    'support' => false,
                ),
            );

            $data = array();
            foreach ($methods as $method => $options) {
                $request = new Request;
                $request->setMethod($method);
                $data[] = array_merge(array($request), $options);
            }

            return $data;
        }
    }

    public function testToHttpRequestWithHeaders()
    {
        $request = new Request(
            array(
                'header' => array(
                    'Content-Type: application/json',
                    'User-Agent: Foo',
                ),
                'authentication' => array(
                    'username' => 'someone',
                    'password' => 'S0M3p455',
                )
            )
        );

        $endpoint = new Endpoint();
        $endpoint->setTimeout(10);

        $httpRequest = $this->adapter->toHttpRequest($request, $endpoint);
        $this->assertEquals(
            array(
                'timeout' => 10,
                'connecttimeout' => 10,
                'dns_cache_timeout' => 10,
                'headers' => array(
                    'Content-Type' => 'application/json',
                    'User-Agent' => 'Foo',
                    'Authorization' => 'Basic c29tZW9uZTpTME0zcDQ1NQ==',
                )
            ),
            $httpRequest->getOptions()
        );
    }

    public function testToHttpRequestWithFile()
    {
        $request = new Request();
        $request->setMethod(Request::METHOD_POST);
        $request->setFileUpload(__FILE__);

        $endpoint = new Endpoint();
        $endpoint->setTimeout(10);

        $httpRequest = $this->adapter->toHttpRequest($request, $endpoint);
        $this->assertEquals(
            array(
                array(
                    'name' => 'content',
                    'type' => 'application/octet-stream; charset=binary',
                    'file' => __FILE__,
                )
            ),
            $httpRequest->getPostFiles()
        );
    }

    public function testToHttpRequestWithDefaultContentType()
    {
        $request = new Request;
        $request->setMethod(Request::METHOD_POST);

        $endpoint = new Endpoint();
        $endpoint->setTimeout(10);

        $httpRequest = $this->adapter->toHttpRequest($request, $endpoint);
        $this->assertEquals(
            array(
                'timeout' => 10,
                'connecttimeout' => 10,
                'dns_cache_timeout' => 10,
                'headers' => array(
                    'Content-Type' => 'text/xml; charset=utf-8',
                )
            ),
            $httpRequest->getOptions()
        );
    }

    public function testExecute()
    {
        $statusCode = 200;
        $statusMessage = 'OK';
        $body = 'data';
        $data = <<<EOF
HTTP/1.1 $statusCode $statusMessage
X-Foo: test

$body
EOF;
        $request = new Request();
        $endpoint = new Endpoint();

        $mockHttpRequest = $this->getMock('HttpRequest');
        $mockHttpRequest->expects($this->once())
                        ->method('send')
                        ->will($this->returnValue(\HttpMessage::factory($data)));
        $mock = $this->getMock('Solarium\Core\Client\Adapter\PeclHttp', array('toHttpRequest'));
        $mock->expects($this->once())
             ->method('toHttpRequest')
             ->with($request, $endpoint)
             ->will($this->returnValue($mockHttpRequest));

        $response = $mock->execute($request, $endpoint);
        $this->assertEquals($body, $response->getBody());
        $this->assertEquals($statusCode, $response->getStatusCode());
        $this->assertEquals($statusMessage, $response->getStatusMessage());
    }

    /**
     * @expectedException \Solarium\Exception\HttpException
     */
    public function testExecuteWithException()
    {
        $endpoint = new Endpoint();
        $endpoint->setPort(-1); // this forces an error
        $request = new Request();
        $this->adapter->execute($request, $endpoint);
    }
}
