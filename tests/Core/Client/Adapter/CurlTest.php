<?php

namespace Solarium\Tests\Core\Client\Adapter;

use PHPUnit\Framework\Constraint\IsType;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Solarium\Core\Client\Adapter\Curl;
use Solarium\Core\Client\Endpoint;
use Solarium\Core\Client\Request;
use Solarium\Exception\HttpException;

class CurlTest extends TestCase
{
    /**
     * @var Curl
     */
    protected $adapter;

    public function setUp()
    {
        if (!function_exists('curl_init')) {
            $this->markTestSkipped('Curl not available, skipping Curl adapter tests');
        }

        $this->adapter = new Curl();
    }

    public function testCheck()
    {
        $data = 'data';
        $headers = ['X-dummy: data'];
        $handler = curl_init();

        // this should be ok, no exception
        $this->adapter->check($data, $headers, $handler);

        $data = '';
        $headers = [];

        $this->expectException(HttpException::class);
        $this->adapter->check($data, $headers, $handler);

        curl_close($handler);
    }

    public function testExecute()
    {
        $headers = ['HTTP/1.0 200 OK'];
        $body = 'data';
        $data = [$body, $headers];

        $request = new Request();
        $endpoint = new Endpoint();

        /** @var Curl|MockObject $mock */
        $mock = $this->getMockBuilder(Curl::class)
            ->setMethods(['getData'])
            ->getMock();

        $mock->expects($this->once())
             ->method('getData')
             ->with($request, $endpoint)
             ->will($this->returnValue($data));

        $response = $mock->execute($request, $endpoint);

        $this->assertSame($data, $response);
    }

    public function testCanCreateHandleForDeleteRequest()
    {
        $request = new Request();
        $request->setMethod(Request::METHOD_DELETE);
        $request->setIsServerRequest(true);
        $endpoint = new Endpoint();

        $curlAdapter = new Curl();
        $handler = $curlAdapter->createHandle($request, $endpoint);

        $this->assertInternalType(IsType::TYPE_RESOURCE, $handler);
        curl_close($handler);
    }
}
