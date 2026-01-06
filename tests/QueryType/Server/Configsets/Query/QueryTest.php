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
    protected Query $query;

    public function setUp(): void
    {
        $this->query = new Query();
    }

    public function testGetType(): void
    {
        $this->assertSame(Client::QUERY_CONFIGSETS, $this->query->getType());
    }

    public function testGetRequestBuilder(): void
    {
        $this->assertInstanceOf(RequestBuilder::class, $this->query->getRequestBuilder());
    }

    public function testGetResponseParser(): void
    {
        $this->assertInstanceOf(ResponseParser::class, $this->query->getResponseParser());
    }

    public function testCreateCreate(): void
    {
        $action = $this->query->createCreate();
        $this->assertInstanceOf(Create::class, $action, 'Can not create CREATE action');
    }

    public function testCreateDelete(): void
    {
        $action = $this->query->createDelete();
        $this->assertInstanceOf(Delete::class, $action, 'Can not create DELETE action');
    }

    public function testCreateList(): void
    {
        $action = $this->query->createList();
        $this->assertInstanceOf(ListConfigsets::class, $action, 'Can not create LIST action');
    }

    public function testCreateUpload(): void
    {
        $action = $this->query->createUpload();
        $this->assertInstanceOf(Upload::class, $action, 'Can not create UPLOAD action');
    }
}
