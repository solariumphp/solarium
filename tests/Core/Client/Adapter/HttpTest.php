<?php

namespace Solarium\Tests\Core\Client\Adapter;

use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Solarium\Core\Client\Adapter\Http;
use Solarium\Core\Client\Endpoint;
use Solarium\Core\Client\Request;
use Solarium\Exception\HttpException;

class HttpTest extends TestCase
{
    /**
     * @var Http
     */
    protected $adapter;

    public function setUp()
    {
        $this->adapter = new Http();
    }

    public function testExecute()
    {
        $data = 'test123';

        $request = new Request();
        $request->setMethod(Request::METHOD_GET);
        $request->setIsServerRequest(true);
        $endpoint = new Endpoint();

        /** @var Http|MockObject $mock */
        $mock = $this->getMockBuilder(Http::class)
            ->setMethods(['getData', 'check'])
            ->getMock();

        $mock->expects($this->once())
             ->method('getData')
             ->with($this->equalTo('http://127.0.0.1:8983/solr/?'), $this->isType('resource'))
             ->will($this->returnValue([$data, ['HTTP 1.1 200 OK']]));

        $mock->execute($request, $endpoint);
    }

    public function testExecuteErrorResponse()
    {
        $data = 'test123';

        $request = new Request();
        $request->setIsServerRequest(true);
        $endpoint = new Endpoint();

        /** @var Http|MockObject $mock */
        $mock = $this->getMockBuilder(Http::class)
            ->setMethods(['getData', 'check'])
            ->getMock();

        $mock->expects($this->once())
             ->method('getData')
             ->with($this->equalTo('http://127.0.0.1:8983/solr/?'), $this->isType('resource'))
             ->will($this->returnValue([$data, ['HTTP 1.1 200 OK']]));
        $mock->expects($this->once())
             ->method('check')
             ->will($this->throwException(new HttpException('HTTP request failed')));

        $this->expectException(HttpException::class);
        $mock->execute($request, $endpoint);
    }

    public function testCheckError()
    {
        $this->expectException(HttpException::class);
        $this->adapter->check(false, []);
    }

    public function testCheckOk()
    {
        $value = $this->adapter->check('dummydata', ['HTTP 1.1 200 OK']);

        $this->assertNull(
            $value
        );
    }

    public function testCreateContextGetRequest()
    {
        $timeout = 13;
        $method = Request::METHOD_HEAD;

        $request = new Request();
        $request->setMethod($method);
        $request->setIsServerRequest(true);
        $endpoint = new Endpoint();
        $endpoint->setTimeout($timeout);

        $context = $this->adapter->createContext($request, $endpoint);

        $this->assertSame(
            ['http' => ['method' => $method, 'timeout' => $timeout]],
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
        $request->setIsServerRequest(true);
        $endpoint = new Endpoint();
        $endpoint->setTimeout($timeout);

        $context = $this->adapter->createContext($request, $endpoint);

        $this->assertSame(
            ['http' => ['method' => $method, 'timeout' => $timeout, 'header' => $header1."\r\n".$header2]],
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
        $request->setIsServerRequest(true);
        $endpoint = new Endpoint();
        $endpoint->setTimeout($timeout);

        $context = $this->adapter->createContext($request, $endpoint);

        $this->assertSame(
            [
                'http' => [
                    'method' => $method,
                    'timeout' => $timeout,
                    'content' => $data,
                    'header' => 'Content-Type: text/xml; charset=UTF-8',
                ],
            ],
            stream_context_get_options($context)
        );
    }

    public function testCreateContextPostFileRequest()
    {
        $timeout = 13;
        $method = Request::METHOD_POST;

        $request = new Request();
        $request->setMethod($method);
        $request->setFileUpload(__FILE__);
        $request->setIsServerRequest(true);
        $endpoint = new Endpoint();
        $endpoint->setTimeout($timeout);

        $context = $this->adapter->createContext($request, $endpoint);

        // Remove content from comparison, since we can't determine the
        // random boundary string.
        $stream_context_get_options = stream_context_get_options($context);
        unset($stream_context_get_options['http']['content'], $stream_context_get_options['http']['header']);

        $this->assertSame(
            [
                'http' => [
                    'method' => $method,
                    'timeout' => $timeout,
                ],
            ],
            $stream_context_get_options
        );
    }

    public function testCreateContextWithAuthorization()
    {
        $timeout = 13;
        $method = Request::METHOD_HEAD;

        $request = new Request();
        $request->setMethod($method);
        $request->setAuthentication('someone', 'S0M3p455');
        $request->setIsServerRequest(true);

        $endpoint = new Endpoint();
        $endpoint->setTimeout($timeout);

        $context = $this->adapter->createContext($request, $endpoint);

        $this->assertSame(
            [
                'http' => [
                    'method' => $method,
                    'timeout' => $timeout,
                    'header' => 'Authorization: Basic c29tZW9uZTpTME0zcDQ1NQ==',
                ],
            ],
            stream_context_get_options($context)
        );
    }

    public function testCreateContextForDeleteRequest()
    {
        $timeout = 22;
        $method = Request::METHOD_DELETE;

        $request = new Request();
        $request->setMethod($method);
        $request->setIsServerRequest(true);
        $endpoint = new Endpoint();
        $endpoint->setTimeout($timeout);

        $context = $this->adapter->createContext($request, $endpoint);

        $this->assertSame(
            [
                'http' => [
                    'method' => $method,
                    'timeout' => $timeout,
                ],
            ],
            stream_context_get_options($context)
        );
    }
}
