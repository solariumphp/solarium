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
    /**
     * @var Query
     */
    protected $query;

    public function setUp(): void
    {
        $this->query = new Query();
    }

    public function testGetType()
    {
        $this->assertSame(Client::QUERY_API, $this->query->getType());
    }

    public function testSetGetVersion()
    {
        $this->assertSame(Request::API_V1, $this->query->getVersion());

        $this->query->setVersion(Request::API_V2);
        $this->assertSame(Request::API_V2, $this->query->getVersion());
    }

    public function testGetRequestBuilder()
    {
        $this->assertInstanceOf(RequestBuilder::class, $this->query->getRequestBuilder());
    }

    public function testGetResponseParser()
    {
        $this->assertInstanceOf(ResponseParser::class, $this->query->getResponseParser());
    }

    public function testGetResultClass()
    {
        $this->assertSame(Result::class, $this->query->getResultClass());
    }
}
