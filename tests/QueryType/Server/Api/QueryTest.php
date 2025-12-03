<?php

namespace Solarium\Tests\QueryType\Server\Api;

use PHPUnit\Framework\TestCase;
use Solarium\Core\Client\Client;
use Solarium\Core\Client\Request;
use Solarium\QueryType\Server\Api\Query;
use Solarium\QueryType\Server\Api\RequestBuilder;
use Solarium\QueryType\Server\Api\ResponseParser;
use Solarium\QueryType\Server\Api\Result;

class QueryTest extends TestCase
{
    protected Query $query;

    public function setUp(): void
    {
        $this->query = new Query();
    }

    public function testGetType(): void
    {
        $this->assertSame(Client::QUERY_API, $this->query->getType());
    }

    public function testSetGetVersion(): void
    {
        $this->assertSame(Request::API_V1, $this->query->getVersion());

        $this->query->setVersion(Request::API_V2);
        $this->assertSame(Request::API_V2, $this->query->getVersion());
    }

    public function testSetGetMethod(): void
    {
        $this->assertSame(Request::METHOD_GET, $this->query->getMethod());

        $this->query->setMethod(Request::METHOD_POST);
        $this->assertSame(Request::METHOD_POST, $this->query->getMethod());
    }

    public function testSetGetAccept(): void
    {
        $this->query->setAccept('example/accept');
        $this->assertSame('example/accept', $this->query->getAccept());
    }

    public function testSetAndGetContentType(): void
    {
        $this->query->setContentType('example/test');

        $this->assertSame(
            'example/test',
            $this->query->getContentType()
        );

        $this->assertNull(
            $this->query->getContentTypeParams()
        );
    }

    public function testSetContentTypeWithParams(): void
    {
        $this->query->setContentType('example/params', ['param' => 'value']);

        $this->assertSame(
            'example/params',
            $this->query->getContentType()
        );

        $this->assertSame(
            ['param' => 'value'],
            $this->query->getContentTypeParams()
        );
    }

    public function testSetContentTypeWithParamsOverridesParams(): void
    {
        $this->query->setContentTypeParams(['param' => 'value']);
        $this->query->setContentType('example/params', ['newparam' => 'newvalue']);

        $this->assertSame(
            'example/params',
            $this->query->getContentType()
        );

        $this->assertSame(
            ['newparam' => 'newvalue'],
            $this->query->getContentTypeParams()
        );
    }

    /**
     * Test that we don't lose the parameters if they are set before the Content-Type.
     */
    public function testSetContentTypeWithoutParamsDoesntOverrideParams(): void
    {
        $this->query->setContentTypeParams(['param' => 'value']);
        $this->query->setContentType('example/params');

        $this->assertSame(
            'example/params',
            $this->query->getContentType()
        );

        $this->assertSame(
            ['param' => 'value'],
            $this->query->getContentTypeParams()
        );
    }

    public function testSetAndGetContentTypeParams(): void
    {
        $this->query->setContentTypeParams(['param' => 'value']);

        $this->assertSame(
            ['param' => 'value'],
            $this->query->getContentTypeParams()
        );
    }

    public function testSetGetRawData(): void
    {
        $this->query->setRawData('raw data');
        $this->assertSame('raw data', $this->query->getRawData());
    }

    public function testGetRequestBuilder(): void
    {
        $this->assertInstanceOf(RequestBuilder::class, $this->query->getRequestBuilder());
    }

    public function testGetResponseParser(): void
    {
        $this->assertInstanceOf(ResponseParser::class, $this->query->getResponseParser());
    }

    public function testGetResultClass(): void
    {
        $this->assertSame(Result::class, $this->query->getResultClass());
    }
}
