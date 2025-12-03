<?php

namespace Solarium\Tests\QueryType\RealtimeGet;

use PHPUnit\Framework\TestCase;
use Solarium\Core\Client\Client;
use Solarium\QueryType\RealtimeGet\Query;

class QueryTest extends TestCase
{
    protected Query $query;

    public function setUp(): void
    {
        $this->query = new Query();
    }

    public function testGetType(): void
    {
        $this->assertSame(Client::QUERY_REALTIME_GET, $this->query->getType());
    }

    public function testGetResponseParser(): void
    {
        $this->assertInstanceOf(
            'Solarium\QueryType\Select\ResponseParser',
            $this->query->getResponseParser()
        );
    }

    public function testGetRequestBuilder(): void
    {
        $this->assertInstanceOf('Solarium\QueryType\RealtimeGet\RequestBuilder', $this->query->getRequestBuilder());
    }

    public function testSetAndGetDocumentClass(): void
    {
        $this->query->setDocumentClass('MyDocument');
        $this->assertSame('MyDocument', $this->query->getDocumentClass());
    }

    public function testGetComponents(): void
    {
        $this->assertSame([], $this->query->getComponents());
    }

    public function testAddId(): void
    {
        $expectedIds = $this->query->getIds();
        $expectedIds[] = 'newid';
        $this->query->addId('newid');
        $this->assertSame($expectedIds, $this->query->getIds());
    }

    public function testClearIds(): void
    {
        $this->query->addId('newid');
        $this->query->clearIds();
        $this->assertSame([], $this->query->getIds());
    }

    public function testAddIds(): void
    {
        $ids = ['id1', 'id2'];

        $this->query->clearIds();
        $this->query->addIds($ids);
        $this->assertSame($ids, $this->query->getIds());
    }

    public function testAddIdsAsStringWithTrim(): void
    {
        $this->query->clearIds();
        $this->query->addIds('id1, id2');
        $this->assertSame(['id1', 'id2'], $this->query->getIds());
    }

    public function testRemoveId(): void
    {
        $this->query->clearIds();
        $this->query->addIds(['id1', 'id2']);
        $this->query->removeId('id1');
        $this->assertSame(['id2'], $this->query->getIds());
    }

    public function testSetIds(): void
    {
        $this->query->clearIds();
        $this->query->addIds(['id1', 'id2']);
        $this->query->setIds(['id3', 'id4']);
        $this->assertSame(['id3', 'id4'], $this->query->getIds());
    }
}
