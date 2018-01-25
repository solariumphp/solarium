<?php

namespace Solarium\Tests\Core\Client\Adapter;

use PHPUnit\Framework\TestCase;
use Solarium\Core\Client\Adapter\ZendHttp as ZendHttpAdapter;
use Solarium\Core\Client\Endpoint;
use Solarium\Core\Client\Request;

class ZendHttpTest extends TestCase
{
    /**
     * @var ZendHttpAdapter
     */
    protected $adapter;

    public function setUp()
    {
        if (!class_exists('\Zend_Http_Client')) {
            $this->markTestSkipped('Zend_Http_Client class not found! skipping test');
        }

        if (!class_exists('Zend_Loader_Autoloader') && !(@include_once 'Zend/Loader/Autoloader.php')) {
            $this->markTestSkipped('ZF not in include_path, skipping ZendHttp adapter tests');
        }

        \Zend_Loader_Autoloader::getInstance();

        $this->adapter = new ZendHttpAdapter();
    }

    public function testForwardingToZendHttpInSetOptions()
    {
        $options = ['optionZ' => 123, 'options' => ['optionX' => 'Y']];
        $adapterOptions = ['optionX' => 'Y'];

        $mock = $this->createMock(\Zend_Http_Client::class);
        $mock->expects($this->once())
                 ->method('setConfig')
                 ->with($this->equalTo($adapterOptions));

        $this->adapter->setZendHttp($mock);
        $this->adapter->setOptions($options);
    }

    public function testSetAndGetZendHttp()
    {
        $dummy = new \stdClass();

        $this->adapter->setZendHttp($dummy);

        $this->assertSame(
            $dummy,
            $this->adapter->getZendHttp()
        );
    }

    public function testGetZendHttpAutoload()
    {
        $options = ['optionZ' => 123, 'options' => ['adapter' => 'Zend_Http_Client_Adapter_Curl']];
        $this->adapter->setOptions($options);

        $zendHttp = $this->adapter->getZendHttp();
        $this->assertThat($zendHttp, $this->isInstanceOf(\Zend_Http_Client::class));
    }

    public function testExecuteGet()
    {
        $method = Request::METHOD_GET;
        $rawData = 'xyz';
        $responseData = 'abc';
        $handler = 'myhandler';
        $headers = [
            'X-test: 123',
        ];
        $params = ['a' => 1, 'b' => 2];

        $request = new Request();
        $request->setMethod($method);
        $request->setHandler($handler);
        $request->setHeaders($headers);
        $request->setRawData($rawData);
        $request->setParams($params);

        $endpoint = new Endpoint();

        $response = new \Zend_Http_Response(200, ['status' => 'HTTP 1.1 200 OK'], $responseData);

        $mock = $this->createMock(\Zend_Http_Client::class);
        $mock->expects($this->once())
                 ->method('setMethod')
                 ->with($this->equalTo($method));
        $mock->expects($this->once())
                 ->method('setUri')
                 ->with($this->equalTo('http://127.0.0.1:8983/solr/myhandler'));
        $mock->expects($this->once())
                 ->method('setHeaders')
                 ->with($this->equalTo(['X-test: 123']));
        $mock->expects($this->once())
                 ->method('setParameterGet')
                 ->with($this->equalTo($params));
        $mock->expects($this->once())
                 ->method('request')
                 ->will($this->returnValue($response));

        $this->adapter->setZendHttp($mock);
        $adapterResponse = $this->adapter->execute($request, $endpoint);

        $this->assertSame(
            $responseData,
            $adapterResponse->getBody()
        );
    }

    public function testExecutePost()
    {
        $method = Request::METHOD_POST;
        $rawData = 'xyz';
        $responseData = 'abc';
        $handler = 'myhandler';
        $headers = [
            'X-test: 123',
        ];
        $params = ['a' => 1, 'b' => 2];

        $request = new Request();
        $request->setMethod($method);
        $request->setHandler($handler);
        $request->setHeaders($headers);
        $request->setRawData($rawData);
        $request->setParams($params);

        $endpoint = new Endpoint();

        $response = new \Zend_Http_Response(200, ['status' => 'HTTP 1.1 200 OK'], $responseData);

        $mock = $this->createMock(\Zend_Http_Client::class);
        $mock->expects($this->once())
                 ->method('setMethod')
                 ->with($this->equalTo($method));
        $mock->expects($this->once())
                 ->method('setUri')
                 ->with($this->equalTo('http://127.0.0.1:8983/solr/myhandler'));
        $mock->expects($this->once())
                 ->method('setHeaders')
                 ->with($this->equalTo(['X-test: 123', 'Content-Type: text/xml; charset=UTF-8']));
        $mock->expects($this->once())
                 ->method('setRawData')
                 ->with($this->equalTo($rawData));
        $mock->expects($this->once())
                 ->method('setParameterGet')
                 ->with($this->equalTo($params));
        $mock->expects($this->once())
                 ->method('request')
                 ->will($this->returnValue($response));

        $this->adapter->setZendHttp($mock);
        $adapterResponse = $this->adapter->execute($request, $endpoint);

        $this->assertSame(
            $responseData,
            $adapterResponse->getBody()
        );
    }

    public function testExecuteErrorResponse()
    {
        $request = new Request();
        $response = new \Zend_Http_Response(404, [], '');
        $endpoint = new Endpoint();

        $mock = $this->createMock(\Zend_Http_Client::class);
        $mock->expects($this->once())
                 ->method('request')
                 ->will($this->returnValue($response));

        $this->adapter->setZendHttp($mock);

        $this->expectException('Solarium\Exception\HttpException');
        $this->adapter->execute($request, $endpoint);
    }

    public function testExecuteHeadRequestReturnsNoData()
    {
        $request = new Request();
        $request->setMethod(Request::METHOD_HEAD);
        $response = new \Zend_Http_Response(200, ['status' => 'HTTP 1.1 200 OK'], 'data');
        $endpoint = new Endpoint();

        $mock = $this->createMock(\Zend_Http_Client::class);
        $mock->expects($this->once())
                 ->method('request')
                 ->will($this->returnValue($response));

        $this->adapter->setZendHttp($mock);
        $response = $this->adapter->execute($request, $endpoint);

        $this->assertSame(
            '',
            $response->getBody()
        );
    }

    public function testExecuteWithInvalidMethod()
    {
        $request = new Request();
        $request->setMethod('invalid');
        $endpoint = new Endpoint();

        $this->expectException('Solarium\Exception\OutOfBoundsException');
        $this->adapter->execute($request, $endpoint);
    }

    public function testExecuteWithFileUpload()
    {
        $request = new Request();
        $request->setMethod(Request::METHOD_POST);
        $request->setFileUpload(__FILE__);
        $endpoint = new Endpoint();
        $response = new \Zend_Http_Response(200, ['status' => 'HTTP 1.1 200 OK'], 'dummy');

        $mock = $this->createMock(\Zend_Http_Client::class);
        $mock->expects($this->once())
             ->method('setFileUpload')
             ->with(
                 $this->equalTo('content'),
                 $this->equalTo('content'),
                 $this->equalTo(file_get_contents(__FILE__)),
                 $this->equalTo('application/octet-stream; charset=binary')
             );
        $mock->expects($this->once())
             ->method('request')
             ->will($this->returnValue($response));

        $this->adapter->setZendHttp($mock);
        $this->adapter->execute($request, $endpoint);
    }
}
