<?php

namespace Solarium\Tests\Core\Client\Adapter;

use PHPUnit\Framework\TestCase;
use Solarium\Core\Client\Adapter\Zend2Http;
use Solarium\Core\Client\Endpoint;
use Solarium\Core\Client\Request;
use Zend\Http\Client as ZendClient;
use Zend\Http\Response as ZendResponse;

class Zend2HttpTest extends TestCase
{
    /**
     * @var Zend2Http
     */
    protected $adapter;

    /**
     * @var ZendClient
     */
    protected $zendClientMock;

    /**
     * @var ZendResponse
     */
    protected $zendResponseMock;

    public function setUp()
    {
        $this->adapter = new Zend2Http();
        $this->zendClientMock = $this->getMockBuilder(ZendClient::class)->setMethods([])->disableOriginalConstructor()->getMock();
        $this->zendResponseMock = $this->getMockBuilder(ZendResponse::class)->setMethods([])->disableOriginalConstructor()->getMock();
        $this->zendClientMock->expects($this->any())->method('send')->willReturn($this->zendResponseMock);
        $this->adapter->setZendHttp($this->zendClientMock);
    }

    protected function fakeValidResponseHeader()
    {
        $this->zendResponseMock->expects($this->any())->method('renderStatusLine')->willReturn('HTTP/1.1 200 OK');
    }

    public function getAllowedHttpMethods()
    {
        return [
            'get' => [Request::METHOD_GET, 'GET', ['foo' => 'bar']],
            'post' => [Request::METHOD_POST, 'POST', ['foo' => 'bar']],
            'head' => [Request::METHOD_HEAD, 'HEAD', ['foo' => 'bar']],
            'delete' => [Request::METHOD_DELETE, 'DELETE', ['foo' => 'bar']],
        ];
    }

    /**
     * @param string $requestMethod
     * @param string $passedHttpMethodString
     * @param array  $passedGetParameters
     * @dataProvider getAllowedHttpMethods
     */
    public function testExecute($requestMethod, $passedHttpMethodString, $passedGetParameters)
    {
        $this->fakeValidResponseHeader();

        $request = new Request();
        $request->setMethod($requestMethod);
        $request->addParams($passedGetParameters);
        $request->setIsServerRequest(true);

        $endpoint = new Endpoint();

        $this->zendClientMock->expects($this->once())->method('setMethod')->with($passedHttpMethodString);

        $this->adapter->execute($request, $endpoint);
    }
}
