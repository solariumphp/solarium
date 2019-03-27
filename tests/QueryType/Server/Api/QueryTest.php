<?php

namespace Solarium\Tests\QueryType\Server\Api;

use PHPUnit\Framework\TestCase;
use Solarium\Core\Client\Client;
use Solarium\Core\Client\Request;
use Solarium\QueryType\Server\Api\Query;

class QueryTest extends TestCase
{
    /**
     * @var Query
     */
    protected $query;

    public function setUp()
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
}
