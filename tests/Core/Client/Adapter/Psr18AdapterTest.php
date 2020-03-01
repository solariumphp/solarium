<?php

namespace Solarium\Tests\Core\Client\Adapter;

use Nyholm\Psr7\Factory\Psr17Factory;
use Nyholm\Psr7\Response;
use PHPUnit\Framework\TestCase;
use Psr\Http\Client\ClientInterface;
use Psr\Http\Message\RequestInterface;
use Solarium\Core\Client\Adapter\Psr18Adapter;
use Solarium\Core\Client\Endpoint;
use Solarium\Core\Client\Request;

class Psr18AdapterTest extends TestCase
{
    public function testExecuteBasicGetRequest(): void
    {
        $client = $this->createMock(ClientInterface::class);
        $client->expects($this->once())
            ->method('sendRequest')
            ->with($this->callback(function (RequestInterface $request) {
                $this->assertSame('http://127.0.0.1:8983/solr/', (string) $request->getUri());
                $this->assertSame(Request::METHOD_GET, $request->getMethod());
                $this->assertSame('', (string) $request->getBody());
                $this->assertSame([
                    'Host' => ['127.0.0.1:8983'],
                    'X-Request-Header' => ['some value', 'and another one'],
                    'Content-Type' => ['application/x-www-form-urlencoded; charset=utf-8'],
                ], $request->getHeaders());

                return true;
            }))
            ->willReturn(new Response(201, ['X-Response-Header' => 'something'], 'some nice body'))
        ;

        $psr17Factory = new Psr17Factory();
        $adapter = new Psr18Adapter($client, $psr17Factory, $psr17Factory);

        $request = new Request();
        $request->setMethod(Request::METHOD_GET);
        $request->addHeader('X-Request-Header: some value');
        $request->addHeader('X-Request-Header: and another one');
        $request->setIsServerRequest(true);

        $response = $adapter->execute($request, new Endpoint());
        $this->assertSame(201, $response->getStatusCode());
        $this->assertSame([
            'HTTP/1.1 201 Created',
            'X-Response-Header: something',
        ], $response->getHeaders());
        $this->assertSame('some nice body', $response->getBody());
    }
}
