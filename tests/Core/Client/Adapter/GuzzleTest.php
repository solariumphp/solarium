<?php

namespace Solarium\Tests\Core\Client\Adapter;

use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Middleware;
use GuzzleHttp\Psr7\Response;
use PHPUnit\Framework\TestCase;
use Solarium\Core\Client\Adapter\Guzzle as GuzzleAdapter;
use Solarium\Core\Client\Endpoint;
use Solarium\Core\Client\Request;
use Solarium\Core\Exception;
use Solarium\Exception\HttpException;

/**
 * @coversDefaultClass \Solarium\Core\Client\Adapter\Guzzle
 * @covers ::<private>
 * @covers ::getGuzzleClient
 */
final class GuzzleTest extends TestCase
{
    /**
     * Prepare each test.
     */
    public function setUp()
    {
        if (!class_exists('\\GuzzleHttp\\Client')) {
            $this->markTestSkipped('Guzzle 6 not installed');
        }
    }

    /**
     * Verify basic behavior of execute().
     *
     * @covers ::execute
     */
    public function testExecuteGet()
    {
        $guzzleResponse = $this->getValidResponse();
        $mockHandler = new MockHandler([$guzzleResponse]);

        $container = [];
        $history = Middleware::history($container);

        $stack = HandlerStack::create($mockHandler);
        $stack->push($history);

        $adapter = new GuzzleAdapter(['handler' => $stack]);

        $request = new Request();
        $request->setMethod(Request::METHOD_GET);
        $request->addHeader('X-PHPUnit: request value');
        $request->setIsServerRequest(true);

        $endpoint = new Endpoint();
        $endpoint->setTimeout(10);

        $response = $adapter->execute($request, $endpoint);
        $this->assertSame('OK', $response->getStatusMessage());
        $this->assertSame(200, $response->getStatusCode());
        $this->assertSame(
            [
                'HTTP/1.1 200 OK',
                'Content-Type: application/json',
                'X-PHPUnit: response value',
            ],
            $response->getHeaders()
        );
        $this->assertSame((string) $guzzleResponse->getBody(), $response->getBody());

        $this->assertCount(1, $container);
        $this->assertSame('GET', $container[0]['request']->getMethod());
        $this->assertSame('request value', $container[0]['request']->getHeaderline('X-PHPUnit'));
    }

    /**
     * Verify execute() with request containing file.
     *
     * @covers ::execute
     */
    public function testExecutePostWithFile()
    {
        $guzzleResponse = $this->getValidResponse();
        $mockHandler = new MockHandler([$guzzleResponse]);

        $container = [];
        $history = Middleware::history($container);

        $stack = HandlerStack::create($mockHandler);
        $stack->push($history);

        $adapter = new GuzzleAdapter(['handler' => $stack]);

        $request = new Request();
        $request->setMethod(Request::METHOD_POST);
        $request->addHeader('X-PHPUnit: request value');
        $request->setIsServerRequest(true);
        $request->setFileUpload(__FILE__);

        $endpoint = new Endpoint();
        $endpoint->setTimeout(10);

        $response = $adapter->execute($request, $endpoint);
        $this->assertSame('OK', $response->getStatusMessage());
        $this->assertSame(200, $response->getStatusCode());
        $this->assertSame(
            [
                'HTTP/1.1 200 OK',
                'Content-Type: application/json',
                'X-PHPUnit: response value',
            ],
            $response->getHeaders()
        );
        $this->assertSame((string) $guzzleResponse->getBody(), $response->getBody());

        $this->assertCount(1, $container);
        $this->assertSame('POST', $container[0]['request']->getMethod());
        $this->assertSame('request value', $container[0]['request']->getHeaderline('X-PHPUnit'));
        $this->assertContains(file_get_contents(__FILE__), (string) $container[0]['request']->getBody());
    }

    /**
     * Verify execute() with request containing raw body.
     *
     * @covers ::execute
     */
    public function testExecutePostWithRawBody()
    {
        $guzzleResponse = $this->getValidResponse();
        $mockHandler = new MockHandler([$guzzleResponse]);

        $container = [];
        $history = Middleware::history($container);

        $stack = HandlerStack::create($mockHandler);
        $stack->push($history);

        $adapter = new GuzzleAdapter(['handler' => $stack]);

        $request = new Request();
        $request->setMethod(Request::METHOD_POST);
        $request->addHeader('X-PHPUnit: request value');
        $request->setIsServerRequest(true);
        $xml = '<root><parent><child>some data</child></parent></root>';
        $request->setRawData($xml);

        $endpoint = new Endpoint();
        $endpoint->setTimeout(10);

        $response = $adapter->execute($request, $endpoint);
        $this->assertSame('OK', $response->getStatusMessage());
        $this->assertSame(200, $response->getStatusCode());
        $this->assertSame(
            [
                'HTTP/1.1 200 OK',
                'Content-Type: application/json',
                'X-PHPUnit: response value',
            ],
            $response->getHeaders()
        );
        $this->assertSame((string) $guzzleResponse->getBody(), $response->getBody());

        $this->assertCount(1, $container);
        $this->assertSame('POST', $container[0]['request']->getMethod());
        $this->assertSame('request value', $container[0]['request']->getHeaderline('X-PHPUnit'));
        $this->assertSame('application/xml; charset=utf-8', $container[0]['request']->getHeaderline('Content-Type'));
        $this->assertSame($xml, (string) $container[0]['request']->getBody());
    }

    /**
     * Verify execute() with GET request containing Authentication.
     *
     * @test
     * @covers ::execute
     */
    public function executeGetWithAuthentication()
    {
        $guzzleResponse = $this->getValidResponse();
        $mockHandler = new MockHandler([$guzzleResponse]);

        $container = [];
        $history = Middleware::history($container);

        $stack = HandlerStack::create($mockHandler);
        $stack->push($history);

        $adapter = new GuzzleAdapter(['handler' => $stack]);

        $request = new Request();
        $request->setMethod(Request::METHOD_GET);
        $request->addHeader('X-PHPUnit: request value');
        $request->setIsServerRequest(true);
        $request->setAuthentication('username', 's3cr3t');

        $endpoint = new Endpoint();
        $endpoint->setTimeout(10);

        $response = $adapter->execute($request, $endpoint);
        $this->assertSame('OK', $response->getStatusMessage());
        $this->assertSame(200, $response->getStatusCode());
        $this->assertSame(
            [
                'HTTP/1.1 200 OK',
                'Content-Type: application/json',
                'X-PHPUnit: response value',
            ],
            $response->getHeaders()
        );
        $this->assertSame((string) $guzzleResponse->getBody(), $response->getBody());

        $this->assertCount(1, $container);
        $this->assertSame('GET', $container[0]['request']->getMethod());
        $this->assertSame('request value', $container[0]['request']->getHeaderline('X-PHPUnit'));
        $this->assertSame(
            'Basic '.base64_encode('username:s3cr3t'),
            $container[0]['request']->getHeaderLine('Authorization')
        );
    }

    /**
     * Verify execute() with GET when guzzle throws an exception.
     *
     * @covers ::execute
     */
    public function testExecuteRequestException()
    {
        $adapter = new GuzzleAdapter();

        $request = new Request();
        $request->setIsServerRequest(true);
        $request->setMethod(Request::METHOD_GET);

        $endpoint = new Endpoint(
            [
                'scheme' => 'silly', //invalid protocol
            ]
        );
        $endpoint->setTimeout(10);

        $this->expectException(HttpException::class);
        $this->expectExceptionMessage('HTTP request failed');

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
            [
                'response' => [
                    'numFound' => 10,
                    'start' => 0,
                    'docs' => [
                        [
                            'id' => '58339e95d5200',
                            'author' => 'Gambardella, Matthew',
                            'title' => "XML Developer's Guide",
                            'genre' => 'Computer',
                            'price' => 44.95,
                            'published' => 970372800,
                            'description' => 'An in-depth look at creating applications with XML.',
                        ],
                    ],
                ],
            ]
        );

        $headers = ['Content-Type' => 'application/json', 'X-PHPUnit' => 'response value'];

        return new Response(200, $headers, $body);
    }
}
