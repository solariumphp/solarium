<?php

namespace Solarium\Tests\QueryType\Server\Configsets\Query;

use PHPUnit\Framework\TestCase;
use Solarium\Core\Client\Client;
use Solarium\QueryType\Server\Configsets\Query\Action\Create;
use Solarium\QueryType\Server\Configsets\Query\Action\Delete;
use Solarium\QueryType\Server\Configsets\Query\Action\ListConfigsets;
use Solarium\QueryType\Server\Configsets\Query\Action\Upload;
use Solarium\QueryType\Server\Configsets\Query\Query;
use Solarium\QueryType\Server\Configsets\RequestBuilder;
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
        $this->assertSame(Client::QUERY_CONFIGSETS, $this->query->getType());
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
        $this->assertInstanceOf(Create::class, $action, 'Can not create CREATE action');
    }

    public function testCreateDelete()
    {
        $action = $this->query->createDelete();
        $this->assertInstanceOf(Delete::class, $action, 'Can not create DELETE action');
    }

    public function testCreateList()
    {
        $action = $this->query->createList();
        $this->assertInstanceOf(ListConfigsets::class, $action, 'Can not create LIST action');
    }

    public function testCreateUpload()
    {
        $action = $this->query->createUpload();
        $this->assertInstanceOf(Upload::class, $action, 'Can not create UPLOAD action');
    }
}
