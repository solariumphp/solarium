<?php

namespace Solarium\Tests\QueryType\Server\Collections\Query;

use PHPUnit\Framework\TestCase;
use Solarium\Core\Client\Client;
use Solarium\QueryType\Server\Collections\Query\Action\Create;
use Solarium\QueryType\Server\Collections\Query\Action\Reload;
use Solarium\QueryType\Server\Collections\Query\Action\ClusterStatus;
use Solarium\QueryType\Server\Collections\Query\Query;
use Solarium\QueryType\Server\Query\RequestBuilder;
use Solarium\QueryType\Server\Query\ResponseParser;

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
        $this->assertSame(Client::QUERY_COLLECTIONS, $this->query->getType());
    }

    public function testGetRequestBuilder()
    {
        $this->assertInstanceOf(RequestBuilder::class, $this->query->getRequestBuilder());
    }

    public function testGetResponseParser()
    {
        $this->assertInstanceOf(ResponseParser::class, $this->query->getResponseParser());
    }

    public function testCreateCreate()
    {
        $action = $this->query->createCreate();
        $this->assertInstanceOf(Create::class, $action, 'Can not create create action');
    }

    public function testCreateReload()
    {
        $action = $this->query->createReload();
        $this->assertInstanceOf(Reload::class, $action, 'Can not create reload action');
    }

    public function testCreateClusterStatus()
    {
        $action = $this->query->createClusterStatus();
        $this->assertInstanceOf(ClusterStatus::class, $action, 'Can not create status action');
    }
}
