<?php

namespace Solarium\Tests\QueryType\Server\Collections\ResponseParser;

use PHPUnit\Framework\TestCase;
use Solarium\Core\Client\Response;
use Solarium\QueryType\Server\Collections\Query\Query;
use Solarium\QueryType\Server\Collections\Result\DeleteResult;

class DeleteTest extends TestCase
{
    public function testParse()
    {
        $data = [
            'responseHeader' => [
                'status' => 0,
                'QTime' => 579,
            ],
            'success' => [
                '127.0.0.1:8983_solr' => [
                    'responseHeader' => [
                        'status' => 0,
                        'QTime' => 108,
                    ],
                ],
                '127.0.0.1:7574_solr' => [
                    'responseHeader' => [
                        'status' => 0,
                        'QTime' => 118,
                    ],
                ],
            ],
        ];

        $query = new Query();
        $response = new Response(json_encode($data), ['HTTP/1.0 200 OK']);
        $result = new DeleteResult($query, $response);

        $this->assertSame(579, $result->getQueryTime());
        $this->assertSame($data, $result->getDeleteStatus());
        // @phpstan-ignore-next-line Will no longer override QueryType::getStatus() in Solarium 8.
        $this->assertSame($data, $result->getStatus());
    }
}
