<?php

namespace Solarium\Tests\Core\Client\Adapter;

use Guzzle\Http\Message\Response;
use Guzzle\Plugin\Mock\MockPlugin;
use PHPUnit\Framework\TestCase;
use Solarium\Core\Client\Adapter\Guzzle3 as GuzzleAdapter;
use Solarium\Core\Client\Endpoint;
use Solarium\Core\Client\Request;
use Solarium\Core\Exception;

/**
 * @coversDefaultClass \Solarium\Core\Client\Adapter\Guzzle3
 * @covers ::<private>
 * @covers ::getGuzzleClient
 */
final class Guzzle3Test extends TestCase
{
    /**
     * @var Guzzle3Adapter
     */
    private $adapter;

    /**
     * Prepare each test.
     */
    public function setUp()
    {
        if (!class_exists('\\Guzzle\\Http\\Client')) {
            $this->markTestSkipped('Guzzle 3 not installed');
        }

        $this->adapter = new GuzzleAdapter();
    }

    /**
     * Verify basic behavior of execute().
     *
     * @test
     * @covers ::execute
     */
    public function executeGet()
    {
        $guzzleResponse = $this->getValidResponse();
        $plugin = new MockPlugin();
        $plugin->addResponse($guzzleResponse);
        $this->adapter->getGuzzleClient()->addSubscriber($plugin);

        $request = new Request();
        $request->setMethod(Request::METHOD_GET);
        $request->addHeader('X-PHPUnit: request value');

        $endpoint = new Endpoint();
        $endpoint->setTimeout(10);

        $response = $this->adapter->execute($request, $endpoint);
        $this->assertSame('OK', $response->getStatusMessage());
        $this->assertSame(200, $response->getStatusCode());
        $this->assertSame(
            array(
                'HTTP/1.1 200 OK',
                'Content-Type: application/json',
                'X-PHPUnit: response value',
            ),
            $response->getHeaders()
        );
        $this->assertSame($guzzleResponse->getBody(true), $response->getBody());

        $receivedRequests = $plugin->getReceivedRequests();

        $this->assertCount(1, $receivedRequests);

        $this->assertSame('GET', $receivedRequests[0]->getMethod());
        $this->assertSame(
            'request value',
            (string) $receivedRequests[0]->getHeader('X-PHPUnit')
        );
    }

    /**
     * Verify execute() with request containing file.
     *
     * @test
     * @covers ::execute
     */
    public function executePostWithFile()
    {
        $guzzleResponse = $this->getValidResponse();
        $plugin = new MockPlugin();
        $plugin->addResponse($guzzleResponse);
        $this->adapter->getGuzzleClient()->addSubscriber($plugin);

        $request = new Request();
        $request->setMethod(Request::METHOD_POST);
        $request->addHeader('X-PHPUnit: request value');
        $request->setFileUpload(__FILE__);

        $endpoint = new Endpoint();
        $endpoint->setTimeout(10);

        $response = $this->adapter->execute($request, $endpoint);
        $this->assertSame('OK', $response->getStatusMessage());
        $this->assertSame(200, $response->getStatusCode());
        $this->assertSame(
            array(
                'HTTP/1.1 200 OK',
                'Content-Type: application/json',
                'X-PHPUnit: response value',
            ),
            $response->getHeaders()
        );
        $this->assertSame($guzzleResponse->getBody(true), $response->getBody());

        $receivedRequests = $plugin->getReceivedRequests();

        $this->assertCount(1, $receivedRequests);

        $this->assertSame('POST', $receivedRequests[0]->getMethod());
        $this->assertStringEqualsFile(__FILE__, (string) $receivedRequests[0]->getBody());
        $this->assertSame(
            'request value',
            (string) $receivedRequests[0]->getHeader('X-PHPUnit')
        );
    }

    /**
     * Verify execute() with request containing raw body.
     *
     * @test
     * @covers ::execute
     */
    public function executePostWithRawBody()
    {
        $guzzleResponse = $this->getValidResponse();
        $plugin = new MockPlugin();
        $plugin->addResponse($guzzleResponse);
        $this->adapter->getGuzzleClient()->addSubscriber($plugin);

        $request = new Request();
        $request->setMethod(Request::METHOD_POST);
        $request->addHeader('X-PHPUnit: request value');
        $xml = '<root><parent><child>some data</child></parent></root>';
        $request->setRawData($xml);

        $endpoint = new Endpoint();
        $endpoint->setTimeout(10);

        $response = $this->adapter->execute($request, $endpoint);
        $this->assertSame('OK', $response->getStatusMessage());
        $this->assertSame(200, $response->getStatusCode());
        $this->assertSame(
            array(
                'HTTP/1.1 200 OK',
                'Content-Type: application/json',
                'X-PHPUnit: response value',
            ),
            $response->getHeaders()
        );
        $this->assertSame($guzzleResponse->getBody(true), $response->getBody());

        $receivedRequests = $plugin->getReceivedRequests();

        $this->assertCount(1, $receivedRequests);

        $this->assertSame('POST', $receivedRequests[0]->getMethod());
        $this->assertSame($xml, (string) $receivedRequests[0]->getBody());
        $this->assertSame(
            'request value',
            (string) $receivedRequests[0]->getHeader('X-PHPUnit')
        );
        $this->assertSame(
            'application/xml; charset=utf-8',
            (string) $receivedRequests[0]->getHeader('Content-Type')
        );
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
        $plugin = new MockPlugin();
        $plugin->addResponse($guzzleResponse);
        $this->adapter->getGuzzleClient()->addSubscriber($plugin);

        $request = new Request();
        $request->setMethod(Request::METHOD_GET);
        $request->addHeader('X-PHPUnit: request value');
        $request->setAuthentication('username', 's3cr3t');

        $endpoint = new Endpoint();
        $endpoint->setTimeout(10);

        $response = $this->adapter->execute($request, $endpoint);
        $this->assertSame('OK', $response->getStatusMessage());
        $this->assertSame(200, $response->getStatusCode());
        $this->assertSame(
            array(
                'HTTP/1.1 200 OK',
                'Content-Type: application/json',
                'X-PHPUnit: response value',
            ),
            $response->getHeaders()
        );
        $this->assertSame($guzzleResponse->getBody(true), $response->getBody());

        $receivedRequests = $plugin->getReceivedRequests();

        $this->assertCount(1, $receivedRequests);

        $this->assertSame('GET', $receivedRequests[0]->getMethod());
        $this->assertSame(
            'request value',
            (string) $receivedRequests[0]->getHeader('X-PHPUnit')
        );

        $this->assertSame(
            'Basic '.base64_encode('username:s3cr3t'),
            (string) $receivedRequests[0]->getHeader('Authorization')
        );
    }

    /**
     * Verify execute() with GET when guzzle throws an exception.
     *
     * @test
     * @covers ::execute
     * @expectedException \Solarium\Exception\HttpException
     * @expectedExceptionMessage HTTP request failed
     */
    public function executeRequestException()
    {
        $request = new Request();
        $request->setMethod(Request::METHOD_GET);

        $endpoint = new Endpoint(
            array(
                'scheme' => 'silly', //invalid protocol
            )
        );

        $this->adapter->execute($request, $endpoint);
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
