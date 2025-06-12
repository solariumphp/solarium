<?php

namespace Solarium\Tests\QueryType\Server\Collections\ResponseParser;

use PHPUnit\Framework\TestCase;
use Solarium\Core\Client\Response;
use Solarium\QueryType\Server\Collections\Query\Query;
use Solarium\QueryType\Server\Collections\Result\CreateResult;

class CreateTest extends TestCase
{
    public function testParse(): void
    {
        $data = [
            'responseHeader' => [
              'status' => 0,
              'QTime' => 1693,
            ],
            'success' => [
                '127.0.0.1:7574_solr' => [
                    'responseHeader' => [
                      'status' => 0,
                      'QTime' => 583,
                    ],
                    'core' => 'test_shard2_replica_n2',
                ],
                '127.0.0.1:8983_solr' => [
                    'responseHeader' => [
                        'status' => 0,
                        'QTime' => 584,
                    ],
                    'core' => 'test_shard1_replica_n1',
                ],
            ],
            'warning' => 'Example warning',
        ];

        $query = new Query();
        $response = new Response(json_encode($data), ['HTTP/1.0 200 OK']);
        $result = new CreateResult($query, $response);

        $this->assertSame(1693, $result->getQueryTime());
        $this->assertSame($data, $result->getCreateStatus());
        // @phpstan-ignore-next-line Will no longer override QueryType::getStatus() in Solarium 8.
        $this->assertSame($data, $result->getStatus());
    }
}
