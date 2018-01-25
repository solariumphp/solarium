<?php

namespace Solarium\Tests\Plugin;

use PHPUnit\Framework\TestCase;
use Solarium\Core\Client\Client;
use Solarium\Plugin\PrefetchIterator;
use Solarium\QueryType\Select\Query\Query;
use Solarium\QueryType\Select\Result\Document;
use Solarium\QueryType\Select\Result\Result;

class PrefetchIteratorTest extends TestCase
{
    /**
     * @var PrefetchIterator
     */
    protected $plugin;

    /**
     * @var Client
     */
    protected $client;

    /**
     * @var Query
     */
    protected $query;

    public function setUp()
    {
        $this->plugin = new PrefetchIterator();

        $this->client = new Client();
        $this->query = $this->client->createSelect();
    }

    public function testSetAndGetPrefetch()
    {
        $this->plugin->setPrefetch(120);
        $this->assertSame(120, $this->plugin->getPrefetch());
    }

    public function testSetAndGetQuery()
    {
        $this->plugin->setQuery($this->query);
        $this->assertSame($this->query, $this->plugin->getQuery());
    }

    public function testCount()
    {
        $result = $this->getResult();
        $mockClient = $this->createMock(Client::class);
        $mockClient->expects($this->exactly(1))
                   ->method('execute')
                   ->with($this->equalTo($this->query), $this->equalTo(null))
                   ->will($this->returnValue($result));

        $this->plugin->initPlugin($mockClient, []);
        $this->plugin->setQuery($this->query);
        $this->assertCount(5, $this->plugin);
    }

    public function testIteratorAndRewind()
    {
        $result = $this->getResult();
        $mockClient = $this->createMock(Client::class);

        // Important: if prefetch or query settings are not changed, the query should be executed only once!
        $mockClient->expects($this->exactly(1))->method('execute')->will($this->returnValue($result));

        $this->plugin->initPlugin($mockClient, []);
        $this->plugin->setQuery($this->query);

        $results1 = [];
        foreach ($this->plugin as $doc) {
            $results1[] = $doc;
        }

        // the second foreach will trigger a rewind, this time include keys
        $results2 = [];
        foreach ($this->plugin as $key => $doc) {
            $results2[$key] = $doc;
        }

        $this->assertSame($result->getDocuments(), $results1);
        $this->assertSame($result->getDocuments(), $results2);
    }

    public function testIteratorResetOnSetPrefetch()
    {
        $result = $this->getResult();
        $mockClient = $this->createMock(Client::class);
        $mockClient->expects($this->exactly(2))->method('execute')->will($this->returnValue($result));

        $this->plugin->initPlugin($mockClient, []);
        $this->plugin->setQuery($this->query);

        $results1 = [];
        foreach ($this->plugin as $doc) {
            $results1[] = $doc;
        }

        $this->plugin->setPrefetch(1000);

        // the second foreach should trigger a reset and a second query execution (checked by mock)
        $results2 = [];
        foreach ($this->plugin as $doc) {
            $results2[] = $doc;
        }

        $this->assertSame($result->getDocuments(), $results1);
        $this->assertSame($result->getDocuments(), $results2);
    }

    public function testIteratorResetOnSetQuery()
    {
        $result = $this->getResult();
        $mockClient = $this->createMock(Client::class);
        $mockClient->expects($this->exactly(2))->method('execute')->will($this->returnValue($result));

        $this->plugin->initPlugin($mockClient, []);
        $this->plugin->setQuery($this->query);

        $results1 = [];
        foreach ($this->plugin as $doc) {
            $results1[] = $doc;
        }

        $this->plugin->setQuery($this->query);

        // the second foreach should trigger a reset and a second query execution (checked by mock)
        $results2 = [];
        foreach ($this->plugin as $doc) {
            $results2[] = $doc;
        }

        $this->assertSame($result->getDocuments(), $results1);
        $this->assertSame($result->getDocuments(), $results2);
    }

    public function getResult()
    {
        $numFound = 5;

        $docs = [
            new Document(['id' => 1, 'title' => 'doc1']),
            new Document(['id' => 2, 'title' => 'doc2']),
            new Document(['id' => 3, 'title' => 'doc3']),
            new Document(['id' => 4, 'title' => 'doc4']),
            new Document(['id' => 5, 'title' => 'doc5']),
        ];

        return new SelectDummy(1, 12, $numFound, $docs, []);
    }

    public function testSetAndGetEndpointAsString()
    {
        $this->assertNull($this->plugin->getEndpoint());
        $this->plugin->setEndpoint('s1');
        $this->assertSame('s1', $this->plugin->getEndpoint());
    }

    public function testWithSpecificEndpoint()
    {
        $result = $this->getResult();
        $mockClient = $this->createMock(Client::class);
        $mockClient->expects($this->exactly(1))
                   ->method('execute')
                   ->with($this->equalTo($this->query), $this->equalTo('s2'))
                   ->will($this->returnValue($result));

        $this->plugin->initPlugin($mockClient, []);
        $this->plugin->setQuery($this->query)->setEndpoint('s2');
        $this->assertCount(5, $this->plugin);
    }

    public function testWithSpecificEndpointOption()
    {
        $result = $this->getResult();
        $mockClient = $this->createMock(Client::class);
        $mockClient->expects($this->exactly(1))
                   ->method('execute')
                   ->with($this->equalTo($this->query), $this->equalTo('s3'))
                   ->will($this->returnValue($result));

        $this->plugin->initPlugin($mockClient, ['endpoint' => 's3']);
        $this->plugin->setQuery($this->query);
        $this->assertCount(5, $this->plugin);
    }
}

class SelectDummy extends Result
{
    protected $parsed = true;

    public function __construct($status, $queryTime, $numfound, $docs, $components)
    {
        $this->numfound = $numfound;
        $this->documents = $docs;
        $this->components = $components;
        $this->queryTime = $queryTime;
        $this->status = $status;
    }
}
