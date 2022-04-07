<?php

namespace Solarium\Tests\Core\Client\State;

use PHPUnit\Framework\TestCase;
use Solarium\Core\Client\State\ClusterState;
use Solarium\Core\Client\State\CollectionState;
use Solarium\Core\Client\State\ShardState;
use Solarium\Exception\RuntimeException;

class ClusterStateTest extends TestCase
{
    /**
     * @var ClusterState
     */
    private $clusterState;

    private $json = '{
  "cluster": {
    "collections": {
      "collection1": {
        "shards": {
          "shard1": {
            "range": "80000000-ffffffff",
            "state": "active",
            "replicas": {
              "core_node1": {
                "state": "active",
                "core": "collection1",
                "node_name": "127.0.1.1:8983_solr",
                "base_url": "http://127.0.1.1:8983/solr",
                "leader": "true"
              },
              "core_node3": {
                "state": "active",
                "core": "collection1",
                "node_name": "127.0.1.1:8900_solr",
                "base_url": "http://127.0.1.1:8900/solr"
              }
            }
          },
          "shard2": {
            "range": "0-7fffffff",
            "state": "active",
            "replicas": {
              "core_node2": {
                "state": "active",
                "core": "collection1",
                "node_name": "127.0.1.1:7574_solr",
                "base_url": "http://127.0.1.1:7574/solr",
                "leader": "true"
              },
              "core_node4": {
                "state": "active",
                "core": "collection1",
                "node_name": "127.0.1.1:7500_solr",
                "base_url": "http://127.0.1.1:7500/solr"
              }
            }
          }
        },
        "maxShardsPerNode": "1",
        "router": {
          "name": "compositeId"
        },
        "replicationFactor": "1",
        "tlogReplicas":"0",
        "znodeVersion": 11,
        "autoCreated": "true",
        "configName": "my_config",
        "aliases": [
          "both_collections"
        ]
      }
    },
    "aliases": {
      "both_collections": "collection1,collection2"
    },
    "roles": {
      "overseer": [
        "127.0.1.1:8983_solr",
        "127.0.1.1:7574_solr"
      ]
    },
    "live_nodes": [
      "127.0.1.1:7574_solr",
      "127.0.1.1:7500_solr",
      "127.0.1.1:8983_solr",
      "127.0.1.1:8900_solr"
    ]
  }
}';

    public function setUp(): void
    {
        $state = json_decode($this->json, true);
        $this->clusterState = new ClusterState($state['cluster']);
    }

    public function testCollectionExists()
    {
        self::assertTrue($this->clusterState->collectionExists('collection1'));
    }

    public function testCollectionState()
    {
        $collectionState = $this->clusterState->getCollectionState('collection1');
        self::assertInstanceOf(CollectionState::class, $collectionState);
        self::assertSame(['both_collections'], $collectionState->getAliases());
        self::assertSame('my_config', $collectionState->getConfigName());
        self::assertSame('collection1', $collectionState->getName());
        self::assertSame(1, $collectionState->getMaxShardsPerNode());
        $shardLeadersBaseUris = [
            'shard1' => 'http://127.0.1.1:8983/solr',
            'shard2' => 'http://127.0.1.1:7574/solr',
        ];
        self::assertSame($shardLeadersBaseUris, $collectionState->getShardLeadersBaseUris());
        $nodeBaseUris = [
            '127.0.1.1:8983_solr' => 'http://127.0.1.1:8983/solr',
            '127.0.1.1:8900_solr' => 'http://127.0.1.1:8900/solr',
            '127.0.1.1:7574_solr' => 'http://127.0.1.1:7574/solr',
            '127.0.1.1:7500_solr' => 'http://127.0.1.1:7500/solr',
        ];
        self::assertSame($nodeBaseUris, $collectionState->getNodesBaseUris());
        self::assertSame(1, $collectionState->getReplicationFactor());
        self::assertSame('compositeId', $collectionState->getRouterName());
        self::assertFalse($collectionState->isAutoAddReplicas());
        self::assertTrue($collectionState->isAutoCreated());
        self::assertSame('0', $collectionState->getTlogReplicas());
        self::assertSame('11', $collectionState->getZnodeVersion());
    }

    public function testCollectionStateToString()
    {
        $collectionState = $this->clusterState->getCollectionState('collection1');
        $expectedString = <<<'EOT'
Solarium\Core\Client\State\CollectionState::__toString
Array
(
    [collection1] => Array
        (
            [shards] => Array
                (
                    [shard1] => Array
                        (
                            [range] => 80000000-ffffffff
                            [state] => active
                            [replicas] => Array
                                (
                                    [core_node1] => Array
                                        (
                                            [state] => active
                                            [core] => collection1
                                            [node_name] => 127.0.1.1:8983_solr
                                            [base_url] => http://127.0.1.1:8983/solr
                                            [leader] => true
                                        )

                                    [core_node3] => Array
                                        (
                                            [state] => active
                                            [core] => collection1
                                            [node_name] => 127.0.1.1:8900_solr
                                            [base_url] => http://127.0.1.1:8900/solr
                                        )

                                )

                        )

                    [shard2] => Array
                        (
                            [range] => 0-7fffffff
                            [state] => active
                            [replicas] => Array
                                (
                                    [core_node2] => Array
                                        (
                                            [state] => active
                                            [core] => collection1
                                            [node_name] => 127.0.1.1:7574_solr
                                            [base_url] => http://127.0.1.1:7574/solr
                                            [leader] => true
                                        )

                                    [core_node4] => Array
                                        (
                                            [state] => active
                                            [core] => collection1
                                            [node_name] => 127.0.1.1:7500_solr
                                            [base_url] => http://127.0.1.1:7500/solr
                                        )

                                )

                        )

                )

            [maxShardsPerNode] => 1
            [router] => Array
                (
                    [name] => compositeId
                )

            [replicationFactor] => 1
            [tlogReplicas] => 0
            [znodeVersion] => 11
            [autoCreated] => true
            [configName] => my_config
            [aliases] => Array
                (
                    [0] => both_collections
                )

        )

)

EOT;

        self::assertSame($expectedString, (string) $collectionState);
    }

    public function testCollectionStateWithNoActiveShards()
    {
        $state = json_decode($this->json, true);

        foreach ($state['cluster']['collections']['collection1']['shards'] as &$shard) {
            $shard['state'] = ShardState::INACTIVE;
        }

        $clusterState = new ClusterState($state['cluster']);
        $collectionState = $clusterState->getCollectionState('collection1');

        self::expectException(RuntimeException::class);
        self::expectExceptionMessage('No Solr nodes are available for this collection.');
        $collectionState->getNodesBaseUris();
    }

    public function testCollectionStateWithNonExistentCollection()
    {
        self::expectException(RuntimeException::class);
        self::expectExceptionMessage("Collection 'collection0' does not exist.");
        $this->clusterState->getCollectionState('collection0');
    }

    public function testCollections()
    {
        self::assertCount(1, $this->clusterState->getCollections());
    }

    public function testLiveNodes()
    {
        $liveNodes = [
            '127.0.1.1:7574_solr',
            '127.0.1.1:7500_solr',
            '127.0.1.1:8983_solr',
            '127.0.1.1:8900_solr',
        ];
        self::assertEquals($liveNodes, $this->clusterState->getLiveNodes());
    }

    public function testAliases()
    {
        $aliases = ['both_collections' => 'collection1,collection2'];
        self::assertEquals($aliases, $this->clusterState->getAliases());
    }

    public function testShardLeaders()
    {
        $collectionState = $this->clusterState->getCollectionState('collection1');
        $shardLeaders = $collectionState->getShardLeaders();
        self::assertCount(2, $shardLeaders);
        self::assertSame('core_node1', $shardLeaders['shard1']->getName());
        self::assertSame('core_node2', $shardLeaders['shard2']->getName());
        self::assertSame('active', $shardLeaders['shard1']->getState());
        self::assertSame('active', $shardLeaders['shard2']->getState());
        self::assertSame('collection1', $shardLeaders['shard1']->getCore());
        self::assertSame('collection1', $shardLeaders['shard2']->getCore());
        self::assertSame('127.0.1.1:8983_solr', $shardLeaders['shard1']->getNodeName());
        self::assertSame('127.0.1.1:7574_solr', $shardLeaders['shard2']->getNodeName());
        self::assertSame('http://127.0.1.1:8983/solr', $shardLeaders['shard1']->getServerUri());
        self::assertSame('http://127.0.1.1:7574/solr', $shardLeaders['shard2']->getServerUri());
        self::assertTrue($shardLeaders['shard1']->isActive());
        self::assertTrue($shardLeaders['shard2']->isActive());
        self::assertTrue($shardLeaders['shard1']->isLeader());
        self::assertTrue($shardLeaders['shard2']->isLeader());
    }

    public function testShards()
    {
        $collectionState = $this->clusterState->getCollectionState('collection1');
        $shards = $collectionState->getShards();
        self::assertCount(2, $shards);
        self::assertSame('shard1', $shards['shard1']->getName());
        self::assertSame('shard2', $shards['shard2']->getName());
        self::assertSame('active', $shards['shard1']->getState());
        self::assertSame('active', $shards['shard2']->getState());

        self::assertSame('active', $shards['shard1']->getState());
        self::assertSame('active', $shards['shard2']->getState());
        self::assertSame('80000000-ffffffff', $shards['shard1']->getRange());
        self::assertSame('0-7fffffff', $shards['shard2']->getRange());
        self::assertCount(2, $shards['shard1']->getReplicas());
        self::assertCount(2, $shards['shard2']->getReplicas());
        $shard1_replica1 = $shards['shard1']->getReplicas()['core_node1'];
        $shard1_replica3 = $shards['shard1']->getReplicas()['core_node3'];
        $shard2_replica2 = $shards['shard2']->getReplicas()['core_node2'];
        $shard2_replica4 = $shards['shard2']->getReplicas()['core_node4'];

        self::assertTrue($shard1_replica1->isActive());
        self::assertTrue($shard1_replica3->isActive());
        self::assertTrue($shard1_replica1->isLeader());
        self::assertFalse($shard1_replica3->isLeader());
        self::assertTrue($shard2_replica2->isActive());
        self::assertTrue($shard2_replica4->isActive());
        self::assertTrue($shard2_replica2->isLeader());
        self::assertFalse($shard2_replica4->isLeader());
    }

    public function testRoles()
    {
        $expectedRoles = [
            'overseer' => [
              '127.0.1.1:8983_solr',
              '127.0.1.1:7574_solr',
            ],
        ];
        self::assertSame($expectedRoles, $this->clusterState->getRoles());
    }
}
