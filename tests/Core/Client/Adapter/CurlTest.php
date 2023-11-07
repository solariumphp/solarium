<?php

namespace Solarium\Tests\Core\Client\Adapter;

use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Solarium\Core\Client\Adapter\Curl;
use Solarium\Core\Client\Endpoint;
use Solarium\Core\Client\Request;
use Solarium\Core\Client\Response;
use Solarium\Exception\HttpException;
use Solarium\Exception\InvalidArgumentException;

class CurlTest extends TestCase
{
    use TimeoutAwareTestTrait;
    use ConnectionTimeoutAwareTestTrait;
    use ProxyAwareTestTrait;

    /**
     * @var Curl
     */
    protected $adapter;

    public function setUp(): void
    {
        if (!\function_exists('curl_init')) {
            $this->markTestSkipped('cURL not available, skipping cURL adapter tests.');
        }

        $this->adapter = new Curl();
    }

    public function testSetProxyConstructor()
    {
        set_error_handler(static function (int $errno, string $errstr): never {
            throw new \Exception($errstr, $errno);
        }, \E_USER_DEPRECATED);

        $this->expectExceptionMessage('Setting proxy as an option is deprecated. Use setProxy() instead.');
        $adapter = new Curl(['proxy' => 'proxy.example.org:1234']);
        $this->assertSame('proxy.example.org:1234', $adapter->getProxy());

        restore_error_handler();
    }

    public function testSetProxyConfigMode()
    {
        set_error_handler(static function (int $errno, string $errstr): never {
            throw new \Exception($errstr, $errno);
        }, \E_USER_DEPRECATED);

        $this->expectExceptionMessage('Setting proxy as an option is deprecated. Use setProxy() instead.');
        $this->adapter->setOptions(['proxy' => 'proxy.example.org:5678']);
        $this->assertSame('proxy.example.org:5678', $this->adapter->getProxy());

        restore_error_handler();
    }

    public function testSetProxyOption()
    {
        set_error_handler(static function (int $errno, string $errstr): never {
            throw new \Exception($errstr, $errno);
        }, \E_USER_DEPRECATED);

        $this->expectExceptionMessage('Setting proxy as an option is deprecated. Use setProxy() instead.');
        $this->adapter->setOption('proxy', 'proxy.example.org:9012');
        $this->assertSame('proxy.example.org:9012', $this->adapter->getProxy());

        restore_error_handler();
    }

    /**
     * Verify that options besides 'proxy' are handled as usual.
     */
    public function testSetNonProxyOption()
    {
        $this->adapter->setOption('foo', 'bar');
        $this->assertSame('bar', $this->adapter->getOption('foo'));
    }

    public function testExecute()
    {
        $headers = ['HTTP/1.0 200 OK'];
        $body = 'data';
        $data = new Response($body, $headers);

        $request = new Request();
        $endpoint = new Endpoint();

        /** @var Curl|MockObject $mock */
        $mock = $this->getMockBuilder(Curl::class)
            ->onlyMethods(['getData'])
            ->getMock();

        $mock->expects($this->once())
             ->method('getData')
             ->with($request, $endpoint)
             ->willReturn($data);

        $response = $mock->execute($request, $endpoint);

        $this->assertSame($data, $response);
    }

    public function testGetResponseThrowsOnFailure()
    {
        $handle = curl_init('example.invalid');
        curl_exec($handle);

        $this->expectException(HttpException::class);
        $this->adapter->getResponse($handle, false);

        curl_close($handle);
    }

    /**
     * @dataProvider methodProvider
     */
    public function testCreateHandleForRequestMethod(string $method)
    {
        $request = new Request();
        $request->setMethod($method);
        $request->setIsServerRequest(true);
        $endpoint = new Endpoint();

        $handle = $this->adapter->createHandle($request, $endpoint);

        $this->assertInstanceOf(\CurlHandle::class, $handle);

        curl_close($handle);
    }

    public function methodProvider(): array
    {
        return [
            [Request::METHOD_GET],
            [Request::METHOD_POST],
            [Request::METHOD_HEAD],
            [Request::METHOD_DELETE],
            [Request::METHOD_PUT],
        ];
    }

    public function testCreateHandleForPostRequestWithFileUpload()
    {
        $tmpfname = tempnam(sys_get_temp_dir(), 'tst');
        file_put_contents($tmpfname, 'Test file contents');

        $request = new Request();
        $request->setMethod(Request::METHOD_POST);
        $request->setFileUpload($tmpfname);
        $request->setIsServerRequest(true);
        $endpoint = new Endpoint();

        $handle = $this->adapter->createHandle($request, $endpoint);

        $this->assertInstanceOf(\CurlHandle::class, $handle);

        curl_close($handle);
    }

    public function testCreateHandleWithCustomRequestHeaders()
    {
        $request = new Request();
        $request->addHeader('X-Header: value');
        $request->setIsServerRequest(true);
        $endpoint = new Endpoint();

        $handle = $this->adapter->createHandle($request, $endpoint);

        $this->assertInstanceOf(\CurlHandle::class, $handle);

        curl_close($handle);
    }

    public function testCreateHandleWithUnknownMethod()
    {
        $request = new Request();
        $request->setMethod('PSOT');
        $request->setIsServerRequest(true);
        $endpoint = new Endpoint();

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('unsupported method: PSOT');
        $handle = $this->adapter->createHandle($request, $endpoint);

        curl_close($handle);
    }

    public function testRequestBasicAuthentication()
    {
        $request = new Request();
        $request->setIsServerRequest(true);
        $request->setAuthentication('foo', 'bar');
        $endpoint = new Endpoint();

        $handle = $this->adapter->createHandle($request, $endpoint);

        $this->assertInstanceOf(\CurlHandle::class, $handle);

        curl_close($handle);
    }

    public function testEndpointBasicAuthentication()
    {
        $request = new Request();
        $request->setIsServerRequest(true);
        $endpoint = new Endpoint();
        $endpoint->setAuthentication('foo', 'bar');

        $handle = $this->adapter->createHandle($request, $endpoint);

        $this->assertInstanceOf(\CurlHandle::class, $handle);

        curl_close($handle);
    }

    public function testAuthorizationToken()
    {
        $request = new Request();
        $request->setIsServerRequest(true);
        $endpoint = new Endpoint();
        $endpoint->setAuthorizationToken('foo', 'bar');

        $handle = $this->adapter->createHandle($request, $endpoint);

        $this->assertInstanceOf(\CurlHandle::class, $handle);

        curl_close($handle);
    }
}
