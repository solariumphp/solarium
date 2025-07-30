<?php

namespace Solarium\Tests\Core\Client\Adapter;

use PHPUnit\Framework\TestCase;
use Solarium\Core\Client\Adapter\AdapterHelper;
use Solarium\Core\Client\Endpoint;
use Solarium\Core\Client\Request;
use Solarium\Exception\HttpException;

class AdapterHelperTest extends TestCase
{
    /**
     * @var Endpoint
     */
    protected $endpoint;

    /**
     * @var Request
     */
    protected $request;

    public function setUp(): void
    {
        $this->endpoint = new Endpoint();
        $this->endpoint->setScheme('scheme');
        $this->endpoint->setHost('example.org');
        $this->endpoint->setPort(1701);
        $this->endpoint->setPath('/testpath/');
        $this->endpoint->setContext('index');

        $this->request = new Request();
        $this->request->setHandler('test');
        $this->request->addParam('foo', 'bar');
    }

    public function testBuildUriWithCore()
    {
        $this->endpoint->setCore('testcore');

        $uri = AdapterHelper::buildUri($this->request, $this->endpoint);

        $this->assertSame('scheme://example.org:1701/testpath/index/testcore/test?foo=bar', $uri);
    }

    public function testBuildUriWithCollection()
    {
        $this->endpoint->setCollection('testcollection');

        $uri = AdapterHelper::buildUri($this->request, $this->endpoint);

        $this->assertSame('scheme://example.org:1701/testpath/index/testcollection/test?foo=bar', $uri);
    }

    public function testBuildUriApiV1()
    {
        $this->request->setIsServerRequest(true);
        $this->request->setApi(Request::API_V1);

        $uri = AdapterHelper::buildUri($this->request, $this->endpoint);

        $this->assertSame('scheme://example.org:1701/testpath/index/test?foo=bar', $uri);
    }

    public function testBuildUriApiV2()
    {
        $this->request->setIsServerRequest(true);
        $this->request->setApi(Request::API_V2);

        $uri = AdapterHelper::buildUri($this->request, $this->endpoint);

        $this->assertSame('scheme://example.org:1701/testpath/api/test?foo=bar', $uri);
    }

    public function testBuildUriWithInvalidBaseUri()
    {
        $this->expectException(HttpException::class);
        AdapterHelper::buildUri($this->request, $this->endpoint);
    }

    public function testBuildUploadBodyFromRequest(): void
    {
        $tmpfname = tempnam(sys_get_temp_dir(), 'tst');
        file_put_contents($tmpfname, 'Test file contents');

        $expectedBodyRegex = <<<'REGEX'
~^--([[:xdigit:]]{32})\r\n
Content-Disposition:\ form-data;\ name="file";\ filename="tst.+?"\r\n
Content-Type:\ application/octet-stream\r\n
\r\n
Test\ file\ contents\r\n
--\1--\r\n
$~xD
REGEX;

        $this->request->setMethod(Request::METHOD_POST);
        $this->request->setFileUpload($tmpfname);

        $body = AdapterHelper::buildUploadBodyFromRequest($this->request);

        $this->assertMatchesRegularExpression($expectedBodyRegex, $body);
    }

    public function testBuildUploadBodyFromRequestWithResource(): void
    {
        $file = fopen('php://memory', 'w+');
        fwrite($file, 'Test resource contents');

        $expectedBodyRegex = <<<'REGEX'
~^--([[:xdigit:]]{32})\r\n
Content-Disposition:\ form-data;\ name="file";\ filename="memory"\r\n
Content-Type:\ application/octet-stream\r\n
\r\n
Test\ resource\ contents\r\n
--\1--\r\n
$~xD
REGEX;

        $this->request->setMethod(Request::METHOD_POST);
        $this->request->setFileUpload($file);

        $body = AdapterHelper::buildUploadBodyFromRequest($this->request);

        $this->assertMatchesRegularExpression($expectedBodyRegex, $body);

        fclose($file);
    }
}
