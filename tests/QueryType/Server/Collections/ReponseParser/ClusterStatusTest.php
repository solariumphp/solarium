<?php

namespace Solarium\Tests\QueryType\Server\Collections\ResponseParser;

use PHPUnit\Framework\TestCase;
use Solarium\Core\Client\Response;
use Solarium\Core\Client\State\ClusterState;
use Solarium\QueryType\Server\Collections\Query\Query;
use Solarium\QueryType\Server\Collections\Result\ClusterStatusResult;

class ClusterStatusTest extends TestCase
{
    public function testParse()
    {
        $data = [
            'responseHeader' => [
                'status' => 0,
                'QTime' => 4,
            ],
            'cluster' => [
                'collections' => [
                    'gettingstarted' => [
                        'pullReplicas' => '0',
                        'replicationFactor' => '2',
                        'shards' => [
                            'shard1' => [
                                'range' => '80000000-ffffffff',
                                'state' => 'active',
                                'replicas' => [
                                    'core_node3' => [
                                        'core' => 'gettingstarted_shard1_replica_n1',
                                        'node_name' => '127.0.0.1:7574_solr',
                                        'base_url' => 'http://127.0.0.1:7574/solr',
                                        'state' => 'active',
                                        'type' => 'NRT',
                                        'force_set_state' => 'false',
                                    ],
                                    'core_node5' => [
                                        'core' => 'gettingstarted_shard1_replica_n2',
                                        'node_name' => '127.0.0.1:8983_solr',
                                        'base_url' => 'http://127.0.0.1:8983/solr',
                                        'state' => 'active',
                                        'type' => 'NRT',
                                        'force_set_state' => 'false',
                                        'leader' => 'true',
                                    ],
                                ],
                                'health' => 'GREEN',
                            ],
                            'shard2' => [
                                'range' => '0-7fffffff',
                                'state' => 'active',
                                'replicas' => [
                                    'core_node6' => [
                                        'core' => 'gettingstarted_shard2_replica_n4',
                                        'node_name' => '127.0.0.1:7574_solr',
                                        'base_url' => 'http://127.0.0.1:7574/solr',
                                        'state' => 'active',
                                        'type' => 'NRT',
                                        'force_set_state' => 'false',
                                    ],
                                    'core_node8' => [
                                        'core' => 'gettingstarted_shard2_replica_n7',
                                        'node_name' => '127.0.0.1:8983_solr',
                                        'base_url' => 'http://127.0.0.1:8983/solr',
                                        'state' => 'active',
                                        'type' => 'NRT',
                                        'force_set_state' => 'false',
                                        'leader' => 'true',
                                    ],
                                ],
                                'health' => 'GREEN',
                            ],
                        ],
                        'router' => [
                            'name' => 'compositeId',
                        ],
                        'maxShardsPerNode' => '-1',
                        'autoAddReplicas' => 'false',
                        'nrtReplicas' => '2',
                        'tlogReplicas' => '0',
                        'health' => 'GREEN',
                        'znodeVersion' => 5,
                        'configName' => 'gettingstarted',
                    ],
                ],
                'live_nodes' => [
                    '127.0.0.1:8983_solr',
                    '127.0.0.1:7574_solr',
                ],
            ],
        ];

        $query = new Query();
        $response = new Response(json_encode($data), ['HTTP/1.0 200 OK']);
        $result = new ClusterStatusResult($query, $response);

        $this->assertSame(0, $result->getStatus());
        $this->assertSame(4, $result->getQueryTime());
        $this->assertEquals(new ClusterState($data['cluster']), $result->getClusterState());
    }

    public function testParseNoCluster()
    {
        $data = [
            'responseHeader' => [
                'status' => 1,
                'QTime' => 2,
            ],
        ];

        $query = new Query();
        $response = new Response(json_encode($data), ['HTTP/1.0 200 OK']);
        $result = new ClusterStatusResult($query, $response);

        $this->assertSame(1, $result->getStatus());
        $this->assertSame(2, $result->getQueryTime());
        $this->assertEquals(new ClusterState([]), $result->getClusterState());
    }
}
