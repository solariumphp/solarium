<?php
/**
 * Copyright 2011 Bas de Nooijer. All rights reserved.
 *
 * Redistribution and use in source and binary forms, with or without
 * modification, are permitted provided that the following conditions are met:
 *
 * 1. Redistributions of source code must retain the above copyright notice,
 *    this list of conditions and the following disclaimer.
 *
 * 2. Redistributions in binary form must reproduce the above copyright notice,
 *    this listof conditions and the following disclaimer in the documentation
 *    and/or other materials provided with the distribution.
 *
 * THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDER AND CONTRIBUTORS "AS IS"
 * AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO, THE
 * IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE
 * ARE DISCLAIMED. IN NO EVENT SHALL THE COPYRIGHT HOLDER OR CONTRIBUTORS BE
 * LIABLE FOR ANY DIRECT, INDIRECT, INCIDENTAL, SPECIAL, EXEMPLARY, OR
 * CONSEQUENTIAL DAMAGES (INCLUDING, BUT NOT LIMITED TO, PROCUREMENT OF
 * SUBSTITUTE GOODS OR SERVICES; LOSS OF USE, DATA, OR PROFITS; OR BUSINESS
 * INTERRUPTION) HOWEVER CAUSED AND ON ANY THEORY OF LIABILITY, WHETHER IN
 * CONTRACT, STRICT LIABILITY, OR TORT (INCLUDING NEGLIGENCE OR OTHERWISE)
 * ARISING IN ANY WAY OUT OF THE USE OF THIS SOFTWARE, EVEN IF ADVISED OF THE
 * POSSIBILITY OF SUCH DAMAGE.
 *
 * The views and conclusions contained in the software and documentation are
 * those of the authors and should not be interpreted as representing official
 * policies, either expressed or implied, of the copyright holder.
 */

namespace Solarium\Tests\QueryType\Select\Query\Component;

use PHPUnit\Framework\TestCase;
use Solarium\Component\DistributedSearch;
use Solarium\QueryType\Select\Query\Query;

class DistributedSearchTest extends TestCase
{
    /**
     * @var DistributedSearch
     */
    protected $distributedSearch;

    public function setUp()
    {
        $this->distributedSearch = new DistributedSearch;
    }

    public function testConfigModeForShards()
    {
        $options = array(
            'shardhandler' => 'dummyhandler',
            'shards' => array(
                'shard1' => 'localhost:8983/solr/shard1',
                'shard2' => 'localhost:8983/solr/shard2',
            )
        );

        $this->distributedSearch->setOptions($options);

        $this->assertSame($options['shardhandler'], $this->distributedSearch->getShardRequestHandler());
        $this->assertSame($options['shards'], $this->distributedSearch->getShards());
    }

    public function testConfigModeForCollections()
    {
        $options = array(
            'collections' => array(
                'collection1' => 'localhost:8983/solr/collection1',
                'collection2' => 'localhost:8983/solr/collection2',
            )
        );

        $this->distributedSearch->setOptions($options);
        $this->assertSame($options['collections'], $this->distributedSearch->getCollections());
    }

    public function testConfigModeForReplicas()
    {
        $options = array(
            'replicas' => array(
                'replica1' => 'localhost:8983/solr/collection1',
                'replica2' => 'localhost:8983/solr/collection2',
            ),
        );

        $this->distributedSearch->setOptions($options);
        $this->assertSame($options['replicas'], $this->distributedSearch->getReplicas());
    }

    public function testGetType()
    {
        $this->assertSame(
            Query::COMPONENT_DISTRIBUTEDSEARCH,
            $this->distributedSearch->getType()
        );
    }

    public function testGetResponseParser()
    {
        $this->assertSame(null, $this->distributedSearch->getResponseParser());
    }

    public function testGetRequestBuilder()
    {
        $this->assertInstanceOf(
            'Solarium\Component\RequestBuilder\DistributedSearch',
            $this->distributedSearch->getRequestBuilder()
        );
    }

    public function testAddShard()
    {
        $this->distributedSearch->addShard('shard1', 'localhost:8983/solr/shard1');
        $shards = $this->distributedSearch->getShards();
        $this->assertSame(
            'localhost:8983/solr/shard1',
            $shards['shard1']
        );
    }

    public function testRemoveShard()
    {
        $this->distributedSearch->addShard('shard1', 'localhost:8983/solr/shard1');
        $this->distributedSearch->removeShard('shard1');
        $shards = $this->distributedSearch->getShards();
        $this->assertFalse(isset($shards['shard1']));
    }

    public function testClearShards()
    {
        $this->distributedSearch->addShards(
            array(
                'shard1' => 'localhost:8983/solr/shard1',
                'shard2' => 'localhost:8983/solr/shard2',
            )
        );
        $this->distributedSearch->clearShards();
        $shards = $this->distributedSearch->getShards();
        $this->assertTrue(is_array($shards));
        $this->assertCount(0, $shards);
    }

    public function testAddShards()
    {
        $shards = array(
            'shard1' => 'localhost:8983/solr/shard1',
            'shard2' => 'localhost:8983/solr/shard2',
        );
        $this->distributedSearch->addShards($shards);
        $this->assertSame($shards, $this->distributedSearch->getShards());
    }

    public function testSetShards()
    {
        $this->distributedSearch->addShards(
            array(
                'shard1' => 'localhost:8983/solr/shard1',
                'shard2' => 'localhost:8983/solr/shard2',
            )
        );
        $this->distributedSearch->setShards(
            array(
                'shard3' => 'localhost:8983/solr/shard3',
                'shard4' => 'localhost:8983/solr/shard4',
                'shard5' => 'localhost:8983/solr/shard5',
            )
        );
        $shards = $this->distributedSearch->getShards();
        $this->assertCount(3, $shards);
        $this->assertSame(
            array(
                'shard3' => 'localhost:8983/solr/shard3',
                'shard4' => 'localhost:8983/solr/shard4',
                'shard5' => 'localhost:8983/solr/shard5',
            ),
            $shards
        );
    }

    public function testSetShardRequestHandler()
    {
        $this->distributedSearch->setShardRequestHandler('dummy');
        $this->assertSame(
            'dummy',
            $this->distributedSearch->getShardRequestHandler()
        );
    }

    public function testAddCollection()
    {
        $this->distributedSearch->addCollection('collection1', 'localhost:8983/solr/collection1');
        $collections = $this->distributedSearch->getCollections();
        $this->assertSame(
            'localhost:8983/solr/collection1',
            $collections['collection1']
        );
    }

    public function testRemoveCollection()
    {
        $this->distributedSearch->addCollection('collection1', 'localhost:8983/solr/collection1');
        $this->distributedSearch->removeCollection('collection1');
        $collections = $this->distributedSearch->getCollections();
        $this->assertFalse(isset($collections['collection1']));
    }

    public function testClearCollections()
    {
        $this->distributedSearch->addCollections(
            array(
                'collection1' => 'localhost:8983/solr/collection1',
                'collection2' => 'localhost:8983/solr/collection2',
            )
        );
        $this->distributedSearch->clearCollections();
        $collections = $this->distributedSearch->getCollections();
        $this->assertTrue(is_array($collections));
        $this->assertCount(0, $collections);
    }

    public function testAddCollections()
    {
        $collections = array(
            'collection1' => 'localhost:8983/solr/collection1',
            'collection2' => 'localhost:8983/solr/collection2',
        );
        $this->distributedSearch->addCollections($collections);
        $this->assertSame($collections, $this->distributedSearch->getCollections());
    }

    public function testSetCollections()
    {
        $this->distributedSearch->addCollections(
            array(
                'collection1' => 'localhost:8983/solr/collection1',
                'collection2' => 'localhost:8983/solr/collection2',
            )
        );
        $this->distributedSearch->setCollections(
            array(
                'collection3' => 'localhost:8983/solr/collection3',
                'collection4' => 'localhost:8983/solr/collection4',
                'collection5' => 'localhost:8983/solr/collection5',
            )
        );
        $collections = $this->distributedSearch->getCollections();
        $this->assertCount(3, $collections);
        $this->assertSame(
            array(
                'collection3' => 'localhost:8983/solr/collection3',
                'collection4' => 'localhost:8983/solr/collection4',
                'collection5' => 'localhost:8983/solr/collection5',
            ),
            $collections
        );
    }

    public function testAddReplica()
    {
        $this->distributedSearch->addReplica('replica1', 'localhost:8983/solr/replica1');
        $replicas = $this->distributedSearch->getReplicas();
        $this->assertSame(
            'localhost:8983/solr/replica1',
            $replicas['replica1']
        );
    }

    public function testRemoveReplica()
    {
        $this->distributedSearch->addReplica('replica1', 'localhost:8983/solr/replica1');
        $this->distributedSearch->removeReplica('replica1');
        $replicas = $this->distributedSearch->getReplicas();
        $this->assertFalse(isset($replicas['replica1']));
    }

    public function testClearReplicas()
    {
        $this->distributedSearch->addReplicas(
            array(
                'replica1' => 'localhost:8983/solr/replica1',
                'replica2' => 'localhost:8983/solr/replica2',
            )
        );
        $this->distributedSearch->clearReplicas();
        $replicas = $this->distributedSearch->getReplicas();
        $this->assertTrue(is_array($replicas));
        $this->assertCount(0, $replicas);
    }

    public function testAddReplicas()
    {
        $replicas = array(
            'replica1' => 'localhost:8983/solr/replica1',
            'replica2' => 'localhost:8983/solr/replica2',
        );
        $this->distributedSearch->addReplicas($replicas);
        $this->assertSame($replicas, $this->distributedSearch->getReplicas());
    }

    public function testSetReplicas()
    {
        $this->distributedSearch->addReplicas(
            array(
                'replica1' => 'localhost:8983/solr/replica1',
                'replica2' => 'localhost:8983/solr/replica2',
            )
        );
        $this->distributedSearch->setReplicas(
            array(
                'replica3' => 'localhost:8983/solr/replica3',
                'replica4' => 'localhost:8983/solr/replica4',
                'replica5' => 'localhost:8983/solr/replica5',
            )
        );
        $replicas = $this->distributedSearch->getReplicas();
        $this->assertCount(3, $replicas);
        $this->assertSame(
            array(
                'replica3' => 'localhost:8983/solr/replica3',
                'replica4' => 'localhost:8983/solr/replica4',
                'replica5' => 'localhost:8983/solr/replica5',
            ),
            $replicas
        );
    }
}
