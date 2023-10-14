<?php

namespace Solarium\Tests\Core\Client;

use PHPUnit\Framework\TestCase;
use Solarium\Core\Client\Response;
use Solarium\Exception\HttpException;

class ResponseTest extends TestCase
{
    protected $headers;

    protected $data;

    /**
     * @var Response
     */
    protected $response;

    public function setUp(): void
    {
        $this->headers = [
            'HTTP/1.0 304 Not Modified',
            'X-Header: value',
        ];
        $this->data = '{"responseHeader":{"status":0,"QTime":1,"params":{"wt":"json","q":"mdsfgdsfgdf"}},'.
            '"response":{"numFound":0,"start":0,"docs":[]}}';
        $this->response = new Response($this->data, $this->headers);
    }

    public function testGetStatusCode()
    {
        $this->assertSame(304, $this->response->getStatusCode());
    }

    public function testGetStatusMessage()
    {
        $this->assertSame('Not Modified', $this->response->getStatusMessage());
    }

    public function testGetHeaders()
    {
        $this->assertSame($this->headers, $this->response->getHeaders());
    }

    public function testGetBody()
    {
        $this->assertSame($this->data, $this->response->getBody());
    }

    public function testMissingStatusCode()
    {
        $headers = ['dummy'];

        $this->expectException(HttpException::class);
        new Response($this->data, $headers);
    }
}
