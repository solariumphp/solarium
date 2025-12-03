<?php

namespace Solarium\Tests\QueryType\Graph;

use PHPUnit\Framework\TestCase;
use Solarium\Core\Client\Client;
use Solarium\QueryType\Graph\Query;
use Solarium\QueryType\Stream\RequestBuilder;

class QueryTest extends TestCase
{
    protected Query $query;

    public function setUp(): void
    {
        $this->query = new Query();
    }

    public function testGetType(): void
    {
        $this->assertSame(Client::QUERY_GRAPH, $this->query->getType());
    }

    public function testGetRequestBuilder(): void
    {
        $this->assertInstanceOf(RequestBuilder::class, $this->query->getRequestBuilder());
    }

    public function testGetResponseParser(): void
    {
        $this->assertNull($this->query->getResponseParser());
    }

    public function testConfigMode(): void
    {
        $q = new Query(['expr' => 'e1']);

        $this->assertSame('e1', $q->getExpression());
    }

    public function testSetAndGetExpression(): void
    {
        $this->query->setExpression('testexpression');
        $this->assertSame('testexpression', $this->query->getExpression());
    }
}
