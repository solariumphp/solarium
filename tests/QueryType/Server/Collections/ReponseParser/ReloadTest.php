<?php

namespace Solarium\Tests\QueryType\Server\Collections\ResponseParser;

use PHPUnit\Framework\TestCase;
use Solarium\Core\Client\Response;
use Solarium\QueryType\Server\Collections\Query\Query;
use Solarium\QueryType\Server\Collections\Result\ReloadResult;

class ReloadTest extends TestCase
{
    public function testParse()
    {
        $data = [
            'responseHeader' => [
                'status' => 0,
                'QTime' => 559,
            ],
            'success' => [
                '127.0.0.1:8983_solr' => [
                    'responseHeader' => [
                        'status' => 0,
                        'QTime' => 499,
                    ],
                ],
                '127.0.0.1:7574_solr' => [
                    'responseHeader' => [
                        'status' => 0,
                        'QTime' => 509,
                    ],
                ],
            ],
        ];

        $query = new Query();
        $response = new Response(json_encode($data), ['HTTP/1.0 200 OK']);
        $result = new ReloadResult($query, $response);

        $this->assertSame(559, $result->getQueryTime());
        $this->assertSame($data, $result->getReloadStatus());
        // @phpstan-ignore-next-line Will no longer override QueryType::getStatus() in Solarium 8.
        $this->assertSame($data, $result->getStatus());
    }
}
