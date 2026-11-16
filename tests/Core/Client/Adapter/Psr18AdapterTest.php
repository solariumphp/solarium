<?php

namespace Solarium\Tests\Core\Client\Adapter;

use Nyholm\Psr7\Factory\Psr17Factory;
use Nyholm\Psr7\Response;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Psr\Http\Client\ClientExceptionInterface;
use Psr\Http\Client\ClientInterface;
use Psr\Http\Message\RequestInterface;
use Solarium\Core\Client\Adapter\Psr18Adapter;
use Solarium\Core\Client\Endpoint;
use Solarium\Core\Client\Request;
use Solarium\Exception\HttpException;

class Psr18AdapterTest extends TestCase
{
    protected MockObject&ClientInterface $client;

    protected Psr17Factory $psr17Factory;

    protected Psr18Adapter $adapter;

    protected Request $request;

    protected Endpoint $endpoint;

    public function setUp(): void
    {
        $this->client = $this->createMock(ClientInterface::class);
        $this->psr17Factory = new Psr17Factory();
        $this->adapter = new Psr18Adapter($this->client, $this->psr17Factory, $this->psr17Factory);
        $this->request = new Request();
        $this->endpoint = new Endpoint();
    }

    public function testExecuteBasicGetRequest(): void
    {
        $this->client->expects($this->once())
            ->method('sendRequest')
            ->with($this->callback(function (RequestInterface $request) {
                $this->assertSame('http://127.0.0.1:8983/solr/', (string) $request->getUri());
                $this->assertSame(Request::METHOD_GET, $request->getMethod());
                $this->assertSame('', (string) $request->getBody());
                $this->assertSame([
                    'Host' => ['127.0.0.1:8983'],
                    'X-Request-Header' => ['some value', 'and another one'],
                ], $request->getHeaders());

                return true;
            }))
            ->willReturn(new Response(201, ['X-Response-Header' => 'something'], 'some nice body'))
        ;

        $this->request->setMethod(Request::METHOD_GET);
        $this->request->addHeader('X-Request-Header: some value');
        $this->request->addHeader('X-Request-Header: and another one');
        $this->request->setIsServerRequest(true);

        $response = $this->adapter->execute($this->request, $this->endpoint);
        $this->assertSame(201, $response->getStatusCode());
        $this->assertSame([
            'HTTP/1.1 201 Created',
            'X-Response-Header: something',
        ], $response->getHeaders());
        $this->assertSame('some nice body', $response->getBody());
    }

    public function testExecutePostRequestWithBody(): void
    {
        $this->client->expects($this->once())
            ->method('sendRequest')
            ->with($this->callback(function (RequestInterface $request) {
                $this->assertSame(Request::METHOD_POST, $request->getMethod());
                $this->assertSame('some data', (string) $request->getBody());
                $this->assertSame([
                    'Host' => ['127.0.0.1:8983'],
                    'Content-Type' => ['application/xml; charset=us-ascii'],
                ], $request->getHeaders());

                return true;
            }))
            ->willReturn(new Response(400, [], 'some nice body'))
        ;

        $this->request->setMethod(Request::METHOD_POST);
        $this->request->setContentType(Request::CONTENT_TYPE_APPLICATION_XML);
        $this->request->addParam('ie', 'us-ascii', true);
        $this->request->setRawData('some data');
        $this->request->setIsServerRequest(true);

        $response = $this->adapter->execute($this->request, $this->endpoint);
        $this->assertSame(400, $response->getStatusCode());
        $this->assertSame('some nice body', $response->getBody());
    }

    public function testExecutePostRequestWithFileUpload(): void
    {
        $tmpfname = tempnam(sys_get_temp_dir(), 'tst');
        file_put_contents($tmpfname, 'Test file contents');

        $expectedBodyRegex = sprintf(<<<'REGEX'
            ~^--%1$s\r\n
            Content-Disposition:\ form-data;\ name="file";\ filename="%2$s"\r\n
            Content-Type:\ application/octet-stream\r\n
            \r\n
            Test\ file\ contents\r\n
            --%1$s--\r\n
            $~xD
            REGEX, preg_quote($this->request->getBoundary(), '~'), preg_quote(basename($tmpfname), '~'));

        $this->client->expects($this->once())
            ->method('sendRequest')
            ->with($this->callback(function (RequestInterface $request) use ($expectedBodyRegex) {
                $this->assertSame(Request::METHOD_POST, $request->getMethod());
                $this->assertMatchesRegularExpression($expectedBodyRegex, (string) $request->getBody());
                $this->assertSame([
                    'Host' => ['127.0.0.1:8983'],
                ], $request->getHeaders());

                return true;
            }))
            ->willReturn(new Response(400, [], 'some nice body'))
        ;

        $this->request->setMethod(Request::METHOD_POST);
        $this->request->setFileUpload($tmpfname);
        $this->request->setIsServerRequest(true);

        $response = $this->adapter->execute($this->request, $this->endpoint);
        $this->assertSame(400, $response->getStatusCode());
        $this->assertSame('some nice body', $response->getBody());
    }

    public function testExecutePutRequest(): void
    {
        $this->client->expects($this->once())
            ->method('sendRequest')
            ->with($this->callback(function (RequestInterface $request) {
                $this->assertSame(Request::METHOD_PUT, $request->getMethod());
                $this->assertSame('some data', (string) $request->getBody());
                $this->assertSame([
                    'Host' => ['127.0.0.1:8983'],
                    'Content-Type' => ['application/json; charset=utf-8'],
                ], $request->getHeaders());

                return true;
            }))
            ->willReturn(new Response(400, [], 'some nice body'))
        ;

        $this->request->setMethod(Request::METHOD_PUT);
        $this->request->setContentType(Request::CONTENT_TYPE_APPLICATION_JSON);
        $this->request->setRawData('some data');
        $this->request->setIsServerRequest(true);

        $response = $this->adapter->execute($this->request, $this->endpoint);
        $this->assertSame(400, $response->getStatusCode());
        $this->assertSame('some nice body', $response->getBody());
    }

    /**
     * @testWith [true]
     *           [false]
     */
    public function testExecuteRequestWithHttpBasicAuthentication(bool $useRequestAuth): void
    {
        $this->client->expects($this->once())
            ->method('sendRequest')
            ->with($this->callback(function (RequestInterface $request) {
                $this->assertSame([
                    'Host' => ['127.0.0.1:8983'],
                    'Content-Type' => ['application/xml'],
                    'Authorization' => [sprintf('Basic %s', base64_encode('foo:bar'))],
                ], $request->getHeaders());

                return true;
            }))
            ->willReturn(new Response(200))
        ;

        $this->request->setMethod(Request::METHOD_GET);
        $this->request->addHeader('Content-Type: application/xml');
        $this->request->setIsServerRequest(true);

        if ($useRequestAuth) {
            $this->request->setAuthentication('foo', 'bar');
        } else {
            $this->endpoint->setAuthentication('foo', 'bar');
        }

        $this->adapter->execute($this->request, $this->endpoint);
    }

    public function testExecuteRequestWithAuthorizationToken(): void
    {
        $this->client->expects($this->once())
            ->method('sendRequest')
            ->with($this->callback(function (RequestInterface $request) {
                $this->assertSame([
                    'Host' => ['127.0.0.1:8983'],
                    'Content-Type' => ['application/xml'],
                    'Authorization' => ['Token foobar'],
                ], $request->getHeaders());

                return true;
            }))
            ->willReturn(new Response(200))
        ;

        $this->request->setMethod(Request::METHOD_GET);
        $this->request->addHeader('Content-Type: application/xml');
        $this->request->setIsServerRequest(true);

        $this->endpoint->setAuthorizationToken('Token', 'foobar');

        $this->adapter->execute($this->request, $this->endpoint);
    }

    /**
     * @testWith [true]
     *           [false]
     */
    public function testExecuteRequestWithHttpBasicAuthenticationMoreImportantThanAuthorizationToken(bool $useRequestAuth): void
    {
        $this->client->expects($this->once())
            ->method('sendRequest')
            ->with($this->callback(function (RequestInterface $request) {
                $this->assertSame([
                    'Host' => ['127.0.0.1:8983'],
                    'Content-Type' => ['application/xml'],
                    'Authorization' => [sprintf('Basic %s', base64_encode('foo:bar'))],
                ], $request->getHeaders());

                return true;
            }))
            ->willReturn(new Response(200))
        ;

        $this->request->setMethod(Request::METHOD_GET);
        $this->request->addHeader('Content-Type: application/xml');
        $this->request->setIsServerRequest(true);

        if ($useRequestAuth) {
            $this->request->setAuthentication('foo', 'bar');
        } else {
            $this->endpoint->setAuthentication('foo', 'bar');
        }

        $this->endpoint->setAuthorizationToken('Token', 'foobar');

        $this->adapter->execute($this->request, $this->endpoint);
    }

    public function testExecuteWithClientException(): void
    {
        $this->client->expects($this->once())
            ->method('sendRequest')
            ->willThrowException(new DummyClientException('Something went wrong.'))
        ;

        $this->request->setMethod(Request::METHOD_GET);
        $this->request->setIsServerRequest(true);

        $this->expectException(HttpException::class);
        $this->expectExceptionMessage('Something went wrong.');
        $this->adapter->execute($this->request, $this->endpoint);
    }
}

class DummyClientException extends \Exception implements ClientExceptionInterface
{
}
