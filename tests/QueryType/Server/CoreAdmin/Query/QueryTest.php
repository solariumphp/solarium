<?php

namespace Solarium\Tests\QueryType\Server\CoreAdmin\Query;

use PHPUnit\Framework\TestCase;
use Solarium\Core\Client\Client;
use Solarium\QueryType\Server\CoreAdmin\Query\Action\Create;
use Solarium\QueryType\Server\CoreAdmin\Query\Action\MergeIndexes;
use Solarium\QueryType\Server\CoreAdmin\Query\Action\Reload;
use Solarium\QueryType\Server\CoreAdmin\Query\Action\Rename;
use Solarium\QueryType\Server\CoreAdmin\Query\Action\RequestRecovery;
use Solarium\QueryType\Server\CoreAdmin\Query\Action\RequestStatus;
use Solarium\QueryType\Server\CoreAdmin\Query\Action\Split;
use Solarium\QueryType\Server\CoreAdmin\Query\Action\Status;
use Solarium\QueryType\Server\CoreAdmin\Query\Action\Swap;
use Solarium\QueryType\Server\CoreAdmin\Query\Action\Unload;
use Solarium\QueryType\Server\CoreAdmin\Query\Query;
use Solarium\QueryType\Server\CoreAdmin\ResponseParser;
use Solarium\QueryType\Server\Query\RequestBuilder;

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
        $this->assertSame(Client::QUERY_CORE_ADMIN, $this->query->getType());
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

    public function testCreateMergeIndexes()
    {
        $action = $this->query->createMergeIndexes();
        $this->assertInstanceOf(MergeIndexes::class, $action, 'Can not create mergeindexes action');
    }

    public function testCreateReload()
    {
        $action = $this->query->createReload();
        $this->assertInstanceOf(Reload::class, $action, 'Can not create reload action');
    }

    public function testCreateRename()
    {
        $action = $this->query->createRename();
        $this->assertInstanceOf(Rename::class, $action, 'Can not create rename action');
    }

    public function testCreateRequestRecovery()
    {
        $action = $this->query->createRequestRecovery();
        $this->assertInstanceOf(RequestRecovery::class, $action, 'Can not create request recovery action');
    }

    public function testCreateRequestStatus()
    {
        $action = $this->query->createRequestStatus();
        $this->assertInstanceOf(RequestStatus::class, $action, 'Can not create request status action');
    }

    public function testCreateSplit()
    {
        $action = $this->query->createSplit();
        $this->assertInstanceOf(Split::class, $action, 'Can not create split action');
    }

    public function testCreateStatus()
    {
        $action = $this->query->createStatus();
        $this->assertInstanceOf(Status::class, $action, 'Can not create status action');
    }

    public function testCreateSwap()
    {
        $action = $this->query->createSwap();
        $this->assertInstanceOf(Swap::class, $action, 'Can not create swap action');
    }

    public function testCreateUnload()
    {
        $action = $this->query->createUnload();
        $this->assertInstanceOf(Unload::class, $action, 'Can not create unload action');
    }
}
