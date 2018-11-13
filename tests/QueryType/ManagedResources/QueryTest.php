<?php

namespace Solarium\Tests\QueryType\ManagedResources\Resources;

use PHPUnit\Framework\TestCase;
use Solarium\Core\Client\Client;
use Solarium\QueryType\ManagedResources\Query\Resources as ResourcesQuery;

class QueryTest extends TestCase
{
    protected $query;

    public function setUp()
    {
        $this->query = new ResourcesQuery();
    }

    public function testGetType()
    {
        $this->assertSame(Client::QUERY_MANAGED_RESOURCES, $this->query->getType());
    }

    public function testGetResponseParser()
    {
        $this->assertInstanceOf(
            'Solarium\QueryType\ManagedResources\ResponseParser\Resources',
            $this->query->getResponseParser()
        );
    }

    public function testGetRequestBuilder()
    {
        $this->assertInstanceOf(
            'Solarium\QueryType\ManagedResources\RequestBuilder\Resources',
            $this->query->getRequestBuilder()
        );
    }
}
