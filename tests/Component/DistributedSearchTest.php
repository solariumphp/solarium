<?php

namespace Solarium\Tests\Component;

use PHPUnit\Framework\TestCase;
use Solarium\Component\DistributedSearch;
use Solarium\QueryType\Select\Query\Query;

class DistributedSearchTest extends TestCase
{
    /**
     * @var DistributedSearch
     */
    protected $distributedSearch;

    public function setUp(): void
    {
        $this->distributedSearch = new DistributedSearch();
    }

    public function testConfigModeForShards(): void
    {
        $options = [
            'shardhandler' => 'dummyhandler',
            'shards' => [
                'shard1' => 'localhost:8983/solr/shard1',
                'shard2' => 'localhost:8983/solr/shard2',
            ],
        ];

        $this->distributedSearch->setOptions($options);

        $this->assertSame($options['shardhandler'], $this->distributedSearch->getShardRequestHandler());
        $this->assertSame($options['shards'], $this->distributedSearch->getShards());
    }

    public function testConfigModeForCollections(): void
    {
        $options = [
            'collections' => [
                'collection1' => 'localhost:8983/solr/collection1',
                'collection2' => 'localhost:8983/solr/collection2',
            ],
        ];

        $this->distributedSearch->setOptions($options);
        $this->assertSame($options['collections'], $this->distributedSearch->getCollections());
    }

    public function testConfigModeForReplicas(): void
    {
        $options = [
            'replicas' => [
                'replica1' => 'localhost:8983/solr/collection1',
                'replica2' => 'localhost:8983/solr/collection2',
            ],
        ];

        $this->distributedSearch->setOptions($options);
        $this->assertSame($options['replicas'], $this->distributedSearch->getReplicas());
    }

    public function testGetType(): void
    {
        $this->assertSame(
            Query::COMPONENT_DISTRIBUTEDSEARCH,
            $this->distributedSearch->getType()
        );
    }

    public function testGetResponseParser(): void
    {
        $this->assertNull($this->distributedSearch->getResponseParser());
    }

    public function testGetRequestBuilder(): void
    {
        $this->assertInstanceOf(
            'Solarium\Component\RequestBuilder\DistributedSearch',
            $this->distributedSearch->getRequestBuilder()
        );
    }

    public function testAddShard(): void
    {
        $this->distributedSearch->addShard('shard1', 'localhost:8983/solr/shard1');
        $shards = $this->distributedSearch->getShards();
        $this->assertSame(
            'localhost:8983/solr/shard1',
            $shards['shard1']
        );
    }

    public function testRemoveShard(): void
    {
        $this->distributedSearch->addShard('shard1', 'localhost:8983/solr/shard1');
        $this->distributedSearch->removeShard('shard1');
        $shards = $this->distributedSearch->getShards();
        $this->assertFalse(isset($shards['shard1']));
    }

    public function testClearShards(): void
    {
        $this->distributedSearch->addShards(
            [
                'shard1' => 'localhost:8983/solr/shard1',
                'shard2' => 'localhost:8983/solr/shard2',
            ]
        );
        $this->distributedSearch->clearShards();
        $shards = $this->distributedSearch->getShards();
        $this->assertTrue(is_array($shards));
        $this->assertCount(0, $shards);
    }

    public function testAddShards(): void
    {
        $shards = [
            'shard1' => 'localhost:8983/solr/shard1',
            'shard2' => 'localhost:8983/solr/shard2',
        ];
        $this->distributedSearch->addShards($shards);
        $this->assertSame($shards, $this->distributedSearch->getShards());
    }

    public function testSetShards(): void
    {
        $this->distributedSearch->addShards(
            [
                'shard1' => 'localhost:8983/solr/shard1',
                'shard2' => 'localhost:8983/solr/shard2',
            ]
        );
        $this->distributedSearch->setShards(
            [
                'shard3' => 'localhost:8983/solr/shard3',
                'shard4' => 'localhost:8983/solr/shard4',
                'shard5' => 'localhost:8983/solr/shard5',
            ]
        );
        $shards = $this->distributedSearch->getShards();
        $this->assertCount(3, $shards);
        $this->assertSame(
            [
                'shard3' => 'localhost:8983/solr/shard3',
                'shard4' => 'localhost:8983/solr/shard4',
                'shard5' => 'localhost:8983/solr/shard5',
            ],
            $shards
        );
    }

    public function testSetShardRequestHandler(): void
    {
        $this->distributedSearch->setShardRequestHandler('dummy');
        $this->assertSame(
            'dummy',
            $this->distributedSearch->getShardRequestHandler()
        );
    }

    public function testAddCollection(): void
    {
        $this->distributedSearch->addCollection('collection1', 'localhost:8983/solr/collection1');
        $collections = $this->distributedSearch->getCollections();
        $this->assertSame(
            'localhost:8983/solr/collection1',
            $collections['collection1']
        );
    }

    public function testRemoveCollection(): void
    {
        $this->distributedSearch->addCollection('collection1', 'localhost:8983/solr/collection1');
        $this->distributedSearch->removeCollection('collection1');
        $collections = $this->distributedSearch->getCollections();
        $this->assertFalse(isset($collections['collection1']));
    }

    public function testClearCollections(): void
    {
        $this->distributedSearch->addCollections(
            [
                'collection1' => 'localhost:8983/solr/collection1',
                'collection2' => 'localhost:8983/solr/collection2',
            ]
        );
        $this->distributedSearch->clearCollections();
        $collections = $this->distributedSearch->getCollections();
        $this->assertTrue(is_array($collections));
        $this->assertCount(0, $collections);
    }

    public function testAddCollections(): void
    {
        $collections = [
            'collection1' => 'localhost:8983/solr/collection1',
            'collection2' => 'localhost:8983/solr/collection2',
        ];
        $this->distributedSearch->addCollections($collections);
        $this->assertSame($collections, $this->distributedSearch->getCollections());
    }

    public function testSetCollections(): void
    {
        $this->distributedSearch->addCollections(
            [
                'collection1' => 'localhost:8983/solr/collection1',
                'collection2' => 'localhost:8983/solr/collection2',
            ]
        );
        $this->distributedSearch->setCollections(
            [
                'collection3' => 'localhost:8983/solr/collection3',
                'collection4' => 'localhost:8983/solr/collection4',
                'collection5' => 'localhost:8983/solr/collection5',
            ]
        );
        $collections = $this->distributedSearch->getCollections();
        $this->assertCount(3, $collections);
        $this->assertSame(
            [
                'collection3' => 'localhost:8983/solr/collection3',
                'collection4' => 'localhost:8983/solr/collection4',
                'collection5' => 'localhost:8983/solr/collection5',
            ],
            $collections
        );
    }

    public function testAddReplica(): void
    {
        $this->distributedSearch->addReplica('replica1', 'localhost:8983/solr/replica1');
        $replicas = $this->distributedSearch->getReplicas();
        $this->assertSame(
            'localhost:8983/solr/replica1',
            $replicas['replica1']
        );
    }

    public function testRemoveReplica(): void
    {
        $this->distributedSearch->addReplica('replica1', 'localhost:8983/solr/replica1');
        $this->distributedSearch->removeReplica('replica1');
        $replicas = $this->distributedSearch->getReplicas();
        $this->assertFalse(isset($replicas['replica1']));
    }

    public function testClearReplicas(): void
    {
        $this->distributedSearch->addReplicas(
            [
                'replica1' => 'localhost:8983/solr/replica1',
                'replica2' => 'localhost:8983/solr/replica2',
            ]
        );
        $this->distributedSearch->clearReplicas();
        $replicas = $this->distributedSearch->getReplicas();
        $this->assertTrue(is_array($replicas));
        $this->assertCount(0, $replicas);
    }

    public function testAddReplicas(): void
    {
        $replicas = [
            'replica1' => 'localhost:8983/solr/replica1',
            'replica2' => 'localhost:8983/solr/replica2',
        ];
        $this->distributedSearch->addReplicas($replicas);
        $this->assertSame($replicas, $this->distributedSearch->getReplicas());
    }

    public function testSetReplicas(): void
    {
        $this->distributedSearch->addReplicas(
            [
                'replica1' => 'localhost:8983/solr/replica1',
                'replica2' => 'localhost:8983/solr/replica2',
            ]
        );
        $this->distributedSearch->setReplicas(
            [
                'replica3' => 'localhost:8983/solr/replica3',
                'replica4' => 'localhost:8983/solr/replica4',
                'replica5' => 'localhost:8983/solr/replica5',
            ]
        );
        $replicas = $this->distributedSearch->getReplicas();
        $this->assertCount(3, $replicas);
        $this->assertSame(
            [
                'replica3' => 'localhost:8983/solr/replica3',
                'replica4' => 'localhost:8983/solr/replica4',
                'replica5' => 'localhost:8983/solr/replica5',
            ],
            $replicas
        );
    }
}
