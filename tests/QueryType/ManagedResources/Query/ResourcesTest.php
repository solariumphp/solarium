<?php

namespace Solarium\Tests\QueryType\ManagedResources\Query;

use PHPUnit\Framework\TestCase;
use Solarium\Core\Client\Client;
use Solarium\QueryType\ManagedResources\Query\Resources as Query;
use Solarium\QueryType\ManagedResources\RequestBuilder\Resources as RequestBuilder;
use Solarium\QueryType\ManagedResources\ResponseParser\Resources as ResponseParser;

class ResourcesTest extends TestCase
{
    protected $query;

    public function setUp(): void
    {
        $this->query = new Query();
    }

    public function testName()
    {
        $this->assertEquals('resources', $this->query->getName());
    }

    public function testQuery()
    {
        $this->assertEquals(Client::QUERY_MANAGED_RESOURCES, $this->query->getType());
    }

    public function testGetRequestBuilder()
    {
        $this->assertInstanceOf(RequestBuilder::class, $this->query->getRequestBuilder());
    }

    public function testGetResponseParser()
    {
        $this->assertInstanceOf(ResponseParser::class, $this->query->getResponseParser());
    }
}
