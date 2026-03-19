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
    use TimeoutAwareTestTrait;
    use ProxyAwareTestTrait;

    protected Http $adapter;

    public function setUp(): void
    {
        $this->adapter = new Http();
    }

    public function testExecute(): void
    {
        $data = 'test123';

        $request = new Request();
        $request->setMethod(Request::METHOD_GET);
        $request->setIsServerRequest(true);
        $endpoint = new Endpoint();

        /** @var MockObject&Http $mock */
        $mock = $this->getMockBuilder(Http::class)
            ->onlyMethods(['getData'])
            ->getMock();

        $mock->expects($this->once())
             ->method('getData')
             ->with($this->equalTo('http://127.0.0.1:8983/solr/'), $this->isType('resource'))
             ->willReturn([$data, ['HTTP/1.1 200 OK']]);

        $mock->execute($request, $endpoint);
    }

    public function testExecuteErrorResponse(): void
    {
        $request = new Request();
        $request->setIsServerRequest(true);
        $endpoint = new Endpoint();

        /** @var MockObject&Http $mock */
        $mock = $this->getMockBuilder(Http::class)
            ->onlyMethods(['getData'])
            ->getMock();

        $mock->expects($this->once())
             ->method('getData')
             ->with($this->equalTo('http://127.0.0.1:8983/solr/'), $this->isType('resource'))
             ->willReturn([false, []]);

        $this->expectException(HttpException::class);
        $mock->execute($request, $endpoint);
    }

    public function testCreateContextGetRequest(): void
    {
        $timeout = 13;
        $method = Request::METHOD_GET;

        $request = new Request();
        $request->setMethod($method);
        $request->setIsServerRequest(true);
        $endpoint = new Endpoint();
        $this->adapter->setTimeout($timeout);

        $context = $this->adapter->createContext($request, $endpoint);

        $this->assertSame(
            [
                'http' => [
                    'method' => $method,
                    'timeout' => $timeout,
                    'protocol_version' => 1.0,
                    'user_agent' => 'Solarium Http Adapter',
                    'ignore_errors' => true,
                ],
            ],
            stream_context_get_options($context)
        );
    }

    public function testCreateContextHeadRequest(): void
    {
        $timeout = 13;
        $method = Request::METHOD_HEAD;

        $request = new Request();
        $request->setMethod($method);
        $request->setIsServerRequest(true);
        $endpoint = new Endpoint();
        $this->adapter->setTimeout($timeout);

        $context = $this->adapter->createContext($request, $endpoint);

        $this->assertSame(
            [
                'http' => [
                    'method' => $method,
                    'timeout' => $timeout,
                    'protocol_version' => 1.0,
                    'user_agent' => 'Solarium Http Adapter',
                    'ignore_errors' => true,
                ],
            ],
            stream_context_get_options($context)
        );
    }

    public function testCreateContextPostRequest(): void
    {
        $timeout = 13;
        $method = Request::METHOD_POST;
        $data = 'test123';

        $request = new Request();
        $request->setMethod($method);
        $request->setContentType(Request::CONTENT_TYPE_APPLICATION_XML);
        $request->addParam('ie', 'us-ascii', true);
        $request->setRawData($data);
        $request->setIsServerRequest(true);
        $endpoint = new Endpoint();
        $this->adapter->setTimeout($timeout);

        $context = $this->adapter->createContext($request, $endpoint);

        $this->assertSame(
            [
                'http' => [
                    'method' => $method,
                    'timeout' => $timeout,
                    'protocol_version' => 1.0,
                    'user_agent' => 'Solarium Http Adapter',
                    'ignore_errors' => true,
                    'content' => $data,
                    'header' => 'Content-Type: application/xml; charset=us-ascii',
                ],
            ],
            stream_context_get_options($context)
        );
    }

    public function testCreateContextPostFileRequest(): void
    {
        $timeout = 13;
        $method = Request::METHOD_POST;

        $request = new Request();
        $request->setMethod($method);
        $request->setFileUpload(__FILE__);
        $request->setIsServerRequest(true);
        $endpoint = new Endpoint();
        $this->adapter->setTimeout($timeout);

        $context = $this->adapter->createContext($request, $endpoint);

        // Remove content from comparison, since we can't determine the
        // random boundary string.
        $stream_context_get_options = stream_context_get_options($context);
        $contentLength = \strlen($stream_context_get_options['http']['content']);
        unset($stream_context_get_options['http']['content']);

        $this->assertSame(
            [
                'http' => [
                    'method' => $method,
                    'timeout' => $timeout,
                    'protocol_version' => 1.0,
                    'user_agent' => 'Solarium Http Adapter',
                    'ignore_errors' => true,
                    'header' => sprintf('Content-Length: %d', $contentLength),
                ],
            ],
            $stream_context_get_options
        );
    }

    public function testCreateContextPutRequest(): void
    {
        $timeout = 13;
        $method = Request::METHOD_PUT;
        $data = 'test123';

        $request = new Request();
        $request->setMethod($method);
        $request->setContentType(Request::CONTENT_TYPE_APPLICATION_JSON);
        $request->setRawData($data);
        $request->setIsServerRequest(true);
        $endpoint = new Endpoint();
        $this->adapter->setTimeout($timeout);

        $context = $this->adapter->createContext($request, $endpoint);

        $this->assertSame(
            [
                'http' => [
                    'method' => $method,
                    'timeout' => $timeout,
                    'protocol_version' => 1.0,
                    'user_agent' => 'Solarium Http Adapter',
                    'ignore_errors' => true,
                    'content' => $data,
                    'header' => 'Connection: Keep-Alive'."\r\n".'Content-Type: application/json; charset=utf-8',
                ],
            ],
            stream_context_get_options($context)
        );
    }

    public function testCreateContextDeleteRequest(): void
    {
        $timeout = 22;
        $method = Request::METHOD_DELETE;

        $request = new Request();
        $request->setMethod($method);
        $request->setIsServerRequest(true);
        $endpoint = new Endpoint();
        $this->adapter->setTimeout($timeout);

        $context = $this->adapter->createContext($request, $endpoint);

        $this->assertSame(
            [
                'http' => [
                    'method' => $method,
                    'timeout' => $timeout,
                    'protocol_version' => 1.0,
                    'user_agent' => 'Solarium Http Adapter',
                    'ignore_errors' => true,
                ],
            ],
            stream_context_get_options($context)
        );
    }

    public function testCreateContextWithHeaders(): void
    {
        $timeout = 13;
        $method = Request::METHOD_HEAD;
        $header1 = 'X-MyHeader-1: dummyvalue 1';
        $header2 = 'X-MyHeader-2: dummyvalue 2';

        $request = new Request();
        $request->setMethod($method);
        $request->setContentType(Request::CONTENT_TYPE_TEXT_PLAIN);
        $request->addHeader($header1);
        $request->addHeader($header2);
        $request->setIsServerRequest(true);
        $endpoint = new Endpoint();
        $this->adapter->setTimeout($timeout);

        $context = $this->adapter->createContext($request, $endpoint);

        $this->assertSame(
            [
                'http' => [
                    'method' => $method,
                    'timeout' => $timeout,
                    'protocol_version' => 1.0,
                    'user_agent' => 'Solarium Http Adapter',
                    'ignore_errors' => true,
                    'header' => $header1."\r\n".$header2."\r\n".'Content-Type: text/plain; charset=utf-8',
                ],
            ],
            stream_context_get_options($context)
        );
    }

    public function testCreateContextWithRequestAuthorization(): void
    {
        $timeout = 13;
        $method = Request::METHOD_HEAD;

        $request = new Request();
        $request->setMethod($method);
        $request->setAuthentication('someone', 'S0M3p455');
        $request->setIsServerRequest(true);

        $endpoint = new Endpoint();
        $this->adapter->setTimeout($timeout);

        $context = $this->adapter->createContext($request, $endpoint);

        $this->assertSame(
            [
                'http' => [
                    'method' => $method,
                    'timeout' => $timeout,
                    'protocol_version' => 1.0,
                    'user_agent' => 'Solarium Http Adapter',
                    'ignore_errors' => true,
                    'header' => 'Authorization: Basic c29tZW9uZTpTME0zcDQ1NQ==',
                ],
            ],
            stream_context_get_options($context)
        );
    }

    public function testCreateContextWithEndpointAuthorization(): void
    {
        $timeout = 13;
        $method = Request::METHOD_HEAD;

        $request = new Request();
        $request->setMethod($method);
        $request->setIsServerRequest(true);

        $endpoint = new Endpoint();
        $endpoint->setAuthentication('someone', 'S0M3p455');

        $this->adapter->setTimeout($timeout);

        $context = $this->adapter->createContext($request, $endpoint);

        $this->assertSame(
            [
                'http' => [
                    'method' => $method,
                    'timeout' => $timeout,
                    'protocol_version' => 1.0,
                    'user_agent' => 'Solarium Http Adapter',
                    'ignore_errors' => true,
                    'header' => 'Authorization: Basic c29tZW9uZTpTME0zcDQ1NQ==',
                ],
            ],
            stream_context_get_options($context)
        );
    }

    public function testCreateContextWithAuthorizationToken(): void
    {
        $timeout = 13;
        $method = Request::METHOD_HEAD;

        $request = new Request();
        $request->setMethod($method);
        $request->setIsServerRequest(true);

        $endpoint = new Endpoint();
        $endpoint->setAuthorizationToken('Token', 'foobar');

        $this->adapter->setTimeout($timeout);

        $context = $this->adapter->createContext($request, $endpoint);

        $this->assertSame(
            [
                'http' => [
                    'method' => $method,
                    'timeout' => $timeout,
                    'protocol_version' => 1.0,
                    'user_agent' => 'Solarium Http Adapter',
                    'ignore_errors' => true,
                    'header' => 'Authorization: Token foobar',
                ],
            ],
            stream_context_get_options($context)
        );
    }

    public function testCreateContextWithRequestAuthorizationMoreImportantThanAuthorizationToken(): void
    {
        $timeout = 13;
        $method = Request::METHOD_HEAD;

        $request = new Request();
        $request->setMethod($method);
        $request->setAuthentication('someone', 'S0M3p455');
        $request->setIsServerRequest(true);

        $endpoint = new Endpoint();
        $endpoint->setAuthorizationToken('Token', 'foobar');

        $this->adapter->setTimeout($timeout);

        $context = $this->adapter->createContext($request, $endpoint);

        $this->assertSame(
            [
                'http' => [
                    'method' => $method,
                    'timeout' => $timeout,
                    'protocol_version' => 1.0,
                    'user_agent' => 'Solarium Http Adapter',
                    'ignore_errors' => true,
                    'header' => 'Authorization: Basic c29tZW9uZTpTME0zcDQ1NQ==',
                ],
            ],
            stream_context_get_options($context)
        );
    }

    public function testCreateContextWithProxy(): void
    {
        $timeout = 13;
        $proxy = 'proxy.example.org:3456';
        $method = Request::METHOD_HEAD;

        $request = new Request();
        $request->setMethod($method);
        $request->setIsServerRequest(true);

        $endpoint = new Endpoint();
        $this->adapter->setTimeout($timeout);
        $this->adapter->setProxy($proxy);

        $context = $this->adapter->createContext($request, $endpoint);

        $this->assertSame(
            [
                'http' => [
                    'method' => $method,
                    'timeout' => $timeout,
                    'protocol_version' => 1.0,
                    'user_agent' => 'Solarium Http Adapter',
                    'ignore_errors' => true,
                    'proxy' => $proxy,
                    'request_fulluri' => true,
                ],
            ],
            stream_context_get_options($context)
        );
    }
}
