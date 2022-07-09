<?php

namespace Solarium\Tests\QueryType\Server\Api;

use PHPUnit\Framework\TestCase;
use Solarium\Core\Client\Request;
use Solarium\QueryType\Server\Api\Query;
use Solarium\QueryType\Server\Api\RequestBuilder;

class RequestBuilderTest extends TestCase
{
    /**
     * @var Query
     */
    protected $query;

    /**
     * @var RequestBuilder
     */
    protected $builder;

    public function setUp(): void
    {
        $this->query = new Query();
        $this->builder = new RequestBuilder();
    }

    public function testBuildParams()
    {
        $request = $this->builder->build($this->query);

        $this->assertEquals(
            [
                'wt' => 'json',
                'json.nl' => 'flat',
            ],
            $request->getParams()
        );
        $this->assertSame('?wt=json&json.nl=flat', $request->getUri());
    }

    public function testBuildHandler()
    {
        $this->query->setHandler('dummy');
        $request = $this->builder->build($this->query);

        $this->assertSame('dummy?wt=json&json.nl=flat', $request->getUri());
    }

    public function testBuildIsServerRequest()
    {
        $request = $this->builder->build($this->query);

        $this->assertTrue($request->getIsServerRequest());
    }

    public function testBuildApiDefault()
    {
        $request = $this->builder->build($this->query);

        $this->assertSame(Request::API_V1, $request->getApi());
    }

    public function testBuildApiV1()
    {
        $this->query->setVersion(Request::API_V1);
        $request = $this->builder->build($this->query);

        $this->assertSame(Request::API_V1, $request->getApi());
    }

    public function testBuildApiV2()
    {
        $this->query->setVersion(Request::API_V2);
        $request = $this->builder->build($this->query);

        $this->assertSame(Request::API_V2, $request->getApi());
    }

    public function testBuildMethodDefault()
    {
        $request = $this->builder->build($this->query);

        $this->assertSame(Request::METHOD_GET, $request->getMethod());
    }

    public function testBuildMethodGet()
    {
        $this->query->setMethod(Request::METHOD_GET);
        $request = $this->builder->build($this->query);

        $this->assertSame(Request::METHOD_GET, $request->getMethod());
    }

    public function testBuildMethodHead()
    {
        $this->query->setMethod(Request::METHOD_HEAD);
        $request = $this->builder->build($this->query);

        $this->assertSame(Request::METHOD_HEAD, $request->getMethod());
    }

    public function testBuildMethodPost()
    {
        $this->query->setMethod(Request::METHOD_POST);
        $request = $this->builder->build($this->query);

        $this->assertSame(Request::METHOD_POST, $request->getMethod());
        $this->assertSame(Request::CONTENT_TYPE_APPLICATION_JSON, $request->getContentType());
    }

    public function testBuildMethodPut()
    {
        $this->query->setMethod(Request::METHOD_PUT);
        $request = $this->builder->build($this->query);

        $this->assertSame(Request::METHOD_PUT, $request->getMethod());
        $this->assertSame(Request::CONTENT_TYPE_APPLICATION_OCTET_STREAM, $request->getContentType());
    }

    public function testBuildAccept()
    {
        $this->query->setAccept('foo/bar');
        $request = $this->builder->build($this->query);

        $this->assertArrayHasKey('Accept: foo/bar', array_flip($request->getHeaders()));
    }

    public function testBuildContentType()
    {
        $this->query->setContentType('example/test');
        $request = $this->builder->build($this->query);

        $this->assertSame('example/test', $request->getContentType());

        $this->query->setMethod(Request::METHOD_POST);
        $request = $this->builder->build($this->query);

        $this->assertSame('example/test', $request->getContentType());

        $this->query->setMethod(Request::METHOD_PUT);
        $request = $this->builder->build($this->query);

        $this->assertSame('example/test', $request->getContentType());
    }

    public function testBuildContentTypeWithParams()
    {
        $this->query->setContentType('example/test', ['foo' => 'bar']);
        $request = $this->builder->build($this->query);

        $this->assertSame('example/test', $request->getContentType());
        $this->assertSame(['foo' => 'bar'], $request->getContentTypeParams());

        $this->query->setMethod(Request::METHOD_POST);
        $request = $this->builder->build($this->query);

        $this->assertSame('example/test', $request->getContentType());
        $this->assertSame(['foo' => 'bar'], $request->getContentTypeParams());

        $this->query->setMethod(Request::METHOD_PUT);
        $request = $this->builder->build($this->query);

        $this->assertSame('example/test', $request->getContentType());
        $this->assertSame(['foo' => 'bar'], $request->getContentTypeParams());
    }

    public function testBuildContentTypeParams()
    {
        $this->query->setContentTypeParams(['foo' => 'bar']);
        $request = $this->builder->build($this->query);

        $this->assertSame(['foo' => 'bar'], $request->getContentTypeParams());

        $this->query->setMethod(Request::METHOD_POST);
        $request = $this->builder->build($this->query);

        $this->assertSame(Request::CONTENT_TYPE_APPLICATION_JSON, $request->getContentType());
        $this->assertSame(['foo' => 'bar'], $request->getContentTypeParams());

        $this->query->setMethod(Request::METHOD_PUT);
        $request = $this->builder->build($this->query);

        $this->assertSame(Request::CONTENT_TYPE_APPLICATION_OCTET_STREAM, $request->getContentType());
        $this->assertSame(['foo' => 'bar'], $request->getContentTypeParams());
    }

    public function testBuildRawData()
    {
        $this->query->setRawData('some data');
        $request = $this->builder->build($this->query);

        $this->assertSame('some data', $request->getRawData());
    }

    public function testBuild()
    {
        $this->query->setHandler('dummy');
        $this->query->setVersion(Request::API_V2);
        $this->query->setMethod(Request::METHOD_POST);
        $this->query->setAccept('foo/bar');
        $this->query->setContentType('example/test', ['foo' => 'bar']);
        $this->query->setRawData('some data');
        $request = $this->builder->build($this->query);

        $this->assertEquals(
            [
                'wt' => 'json',
                'json.nl' => 'flat',
            ],
            $request->getParams()
        );
        $this->assertSame(Request::API_V2, $request->getApi());
        $this->assertSame(Request::METHOD_POST, $request->getMethod());
        $this->assertTrue($request->getIsServerRequest());
        $this->assertSame('dummy?wt=json&json.nl=flat', $request->getUri());
        $this->assertArrayHasKey('Accept: foo/bar', array_flip($request->getHeaders()));
        $this->assertSame('example/test', $request->getContentType());
        $this->assertSame(['foo' => 'bar'], $request->getContentTypeParams());
        $this->assertSame('some data', $request->getRawData());
    }
}
