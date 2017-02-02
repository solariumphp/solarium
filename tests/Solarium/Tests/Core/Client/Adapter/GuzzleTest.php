<?php
/**
 * Copyright 2011 Bas de Nooijer. All rights reserved.
 * * Redistribution and use in source and binary forms, with or without
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
 *
 * @copyright Copyright 2011 Bas de Nooijer <solarium@raspberry.nl>
 * @license http://github.com/basdenooijer/solarium/raw/master/COPYING
 *
 * @link http://www.solarium-project.org/
 */

namespace Solarium\Tests\Core\Client\Adapter;

use GuzzleHttp\Client as GuzzleClient;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Middleware;
use GuzzleHttp\Psr7\Response;
use Solarium\Core\Client\Adapter\Guzzle as GuzzleAdapter;
use Solarium\Core\Client\Endpoint;
use Solarium\Core\Client\Request;
use Solarium\Core\Exception;

/**
 * @coversDefaultClass \Solarium\Core\Client\Adapter\Guzzle
 * @covers ::<private>
 * @covers ::getGuzzleClient
 */
final class GuzzleAdapterTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Prepare each test.
     *
     * @return void
     */
    public function setUp()
    {
        if (!class_exists('\\GuzzleHttp\\Client')) {
            $this->markTestSkipped('Guzzle 6 not installed');
        }
    }

    /**
     * Verify basic behavior of execute()
     *
     * @test
     * @covers ::execute
     *
     * @return void
     */
    public function executeGet()
    {
        $guzzleResponse = $this->getValidResponse();
        $mockHandler = new MockHandler(array($guzzleResponse));

        $container = array();
        $history = Middleware::history($container);

        $stack = HandlerStack::create($mockHandler);
        $stack->push($history);

        $adapter = new GuzzleAdapter(array('handler' => $stack));

        $request = new Request();
        $request->setMethod(Request::METHOD_GET);
        $request->addHeader('X-PHPUnit: request value');

        $endpoint = new Endpoint();
        $endpoint->setTimeout(10);

        $response = $adapter->execute($request, $endpoint);
        $this->assertSame('OK', $response->getStatusMessage());
        $this->assertSame('200', $response->getStatusCode());
        $this->assertSame(
            array(
                'HTTP/1.1 200 OK',
                'Content-Type: application/json',
                'X-PHPUnit: response value',
            ),
            $response->getHeaders()
        );
        $this->assertSame((string)$guzzleResponse->getBody(), $response->getBody());

        $this->assertCount(1, $container);
        $this->assertSame('GET', $container[0]['request']->getMethod());
        $this->assertSame('request value', $container[0]['request']->getHeaderline('X-PHPUnit'));
    }

    /**
     * Verify execute() with request containing file
     *
     * @test
     * @covers ::execute
     *
     * @return void
     */
    public function executePostWithFile()
    {
        $guzzleResponse = $this->getValidResponse();
        $mockHandler = new MockHandler(array($guzzleResponse));

        $container = array();
        $history = Middleware::history($container);

        $stack = HandlerStack::create($mockHandler);
        $stack->push($history);

        $adapter = new GuzzleAdapter(array('handler' => $stack));

        $request = new Request();
        $request->setMethod(Request::METHOD_POST);
        $request->addHeader('X-PHPUnit: request value');
        $request->setFileUpload(__FILE__);

        $endpoint = new Endpoint();
        $endpoint->setTimeout(10);

        $response = $adapter->execute($request, $endpoint);
        $this->assertSame('OK', $response->getStatusMessage());
        $this->assertSame('200', $response->getStatusCode());
        $this->assertSame(
            array(
                'HTTP/1.1 200 OK',
                'Content-Type: application/json',
                'X-PHPUnit: response value',
            ),
            $response->getHeaders()
        );
        $this->assertSame((string)$guzzleResponse->getBody(), $response->getBody());

        $this->assertCount(1, $container);
        $this->assertSame('POST', $container[0]['request']->getMethod());
        $this->assertSame('request value', $container[0]['request']->getHeaderline('X-PHPUnit'));
        $this->assertSame(file_get_contents(__FILE__), (string)$container[0]['request']->getBody());
    }

    /**
     * Verify execute() with request containing raw body
     *
     * @test
     * @covers ::execute
     *
     * @return void
     */
    public function executePostWithRawBody()
    {
        $guzzleResponse = $this->getValidResponse();
        $mockHandler = new MockHandler(array($guzzleResponse));

        $container = array();
        $history = Middleware::history($container);

        $stack = HandlerStack::create($mockHandler);
        $stack->push($history);

        $adapter = new GuzzleAdapter(array('handler' => $stack));

        $request = new Request();
        $request->setMethod(Request::METHOD_POST);
        $request->addHeader('X-PHPUnit: request value');
        $xml = '<root><parent><child>some data</child></parent></root>';
        $request->setRawData($xml);

        $endpoint = new Endpoint();
        $endpoint->setTimeout(10);

        $response = $adapter->execute($request, $endpoint);
        $this->assertSame('OK', $response->getStatusMessage());
        $this->assertSame('200', $response->getStatusCode());
        $this->assertSame(
            array(
                'HTTP/1.1 200 OK',
                'Content-Type: application/json',
                'X-PHPUnit: response value',
            ),
            $response->getHeaders()
        );
        $this->assertSame((string)$guzzleResponse->getBody(), $response->getBody());

        $this->assertCount(1, $container);
        $this->assertSame('POST', $container[0]['request']->getMethod());
        $this->assertSame('request value', $container[0]['request']->getHeaderline('X-PHPUnit'));
        $this->assertSame('application/xml; charset=utf-8', $container[0]['request']->getHeaderline('Content-Type'));
        $this->assertSame($xml, (string)$container[0]['request']->getBody());
    }

    /**
     * Verify execute() with GET request containing Authentication.
     *
     * @test
     * @covers ::execute
     *
     * @return void
     */
    public function executeGetWithAuthentication()
    {
        $guzzleResponse = $this->getValidResponse();
        $mockHandler = new MockHandler(array($guzzleResponse));

        $container = array();
        $history = Middleware::history($container);

        $stack = HandlerStack::create($mockHandler);
        $stack->push($history);

        $adapter = new GuzzleAdapter(array('handler' => $stack));

        $request = new Request();
        $request->setMethod(Request::METHOD_GET);
        $request->addHeader('X-PHPUnit: request value');
        $request->setAuthentication('username', 's3cr3t');

        $endpoint = new Endpoint();
        $endpoint->setTimeout(10);

        $response = $adapter->execute($request, $endpoint);
        $this->assertSame('OK', $response->getStatusMessage());
        $this->assertSame('200', $response->getStatusCode());
        $this->assertSame(
            array(
                'HTTP/1.1 200 OK',
                'Content-Type: application/json',
                'X-PHPUnit: response value',
            ),
            $response->getHeaders()
        );
        $this->assertSame((string)$guzzleResponse->getBody(), $response->getBody());

        $this->assertCount(1, $container);
        $this->assertSame('GET', $container[0]['request']->getMethod());
        $this->assertSame('request value', $container[0]['request']->getHeaderline('X-PHPUnit'));
        $this->assertSame(
            'Basic ' . base64_encode('username:s3cr3t'),
            $container[0]['request']->getHeaderLine('Authorization')
        );
    }

    /**
     * Verify execute() with GET when guzzle throws an exception.
     *
     * @test
     * @covers ::execute
     * @expectedException \Solarium\Exception\HttpException
     * @expectedExceptionMessage HTTP request failed
     *
     * @return void
     */
    public function executeRequestException()
    {
        $adapter = new GuzzleAdapter();

        $request = new Request();
        $request->setMethod(Request::METHOD_GET);

        $endpoint = new Endpoint(
            array(
                'scheme'  => 'silly', //invalid protocol
            )
        );
        $endpoint->setTimeout(10);

        $adapter->execute($request, $endpoint);
    }

    /**
     * Helper method to create a valid Guzzle response.
     *
     * @return Response
     */
    private function getValidResponse()
    {
        $body = json_encode(
            array(
                'response' => array(
                    'numFound' => 10,
                    'start' => 0,
                    'docs' => array(
                        array(
                            'id' => '58339e95d5200',
                            'author' => 'Gambardella, Matthew',
                            'title' => "XML Developer's Guide",
                            'genre' => 'Computer',
                            'price' => 44.95,
                            'published' => 970372800,
                            'description' => 'An in-depth look at creating applications with XML.',
                        ),
                    ),
                ),
            )
        );

        $headers = array('Content-Type' => 'application/json', 'X-PHPUnit' => 'response value');
        return new Response(200, $headers, $body);
    }
}
