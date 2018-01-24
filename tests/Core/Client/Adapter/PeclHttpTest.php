<?php

namespace Solarium\Tests\Core\Client\Adapter;

use HttpRequest;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Solarium\Core\Client\Adapter\PeclHttp;
use Solarium\Core\Client\Adapter\PeclHttp as PeclHttpAdapter;
use Solarium\Core\Client\Endpoint;
use Solarium\Core\Client\Request;
use Solarium\Exception\ExceptionInterface;

class PeclHttpTest extends TestCase
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
     * @param mixed $request
     * @param mixed $method
     * @param mixed $support
     */
    public function testToHttpRequestWithMethod($request, $method, $support)
    {
        $endpoint = new Endpoint();

        try {
            $httpRequest = $this->adapter->toHttpRequest($request, $endpoint);
            $this->assertSame($httpRequest->getMethod(), $method);
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
        $this->assertSame(
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
        $this->assertSame(
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
        $this->assertSame(
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

        $mockHttpRequest = $this->createMock(HttpRequest::class);
        $mockHttpRequest->expects($this->once())
                        ->method('send')
                        ->will($this->returnValue(\HttpMessage::factory($data)));

        /** @var PeclHttp|MockObject $mock */
        $mock = $this->getMockBuilder(PeclHttp::class)
            ->setMethods(array('toHttpRequest'))
        ->getMock();
        $mock->expects($this->once())
             ->method('toHttpRequest')
             ->with($request, $endpoint)
             ->will($this->returnValue($mockHttpRequest));

        $response = $mock->execute($request, $endpoint);
        $this->assertSame($body, $response->getBody());
        $this->assertSame($statusCode, $response->getStatusCode());
        $this->assertSame($statusMessage, $response->getStatusMessage());
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
