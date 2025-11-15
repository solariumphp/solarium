<?php

namespace Solarium\Tests\QueryType\Server\CoreAdmin;

use PHPUnit\Framework\TestCase;
use Solarium\Core\Client\Response;
use Solarium\QueryType\Server\CoreAdmin\Query\Query;
use Solarium\QueryType\Server\CoreAdmin\ResponseParser;
use Solarium\QueryType\Server\CoreAdmin\Result\Result;

class ResponseParserTest extends TestCase
{
    public function testParseWithResponseProperty(): void
    {
        $query = new Query();
        $action = $query->createSplit();
        $query->setAction($action);

        $data = [
            'response' => [
                'timing' => [
                    'time' => 318,
                    'doSplit' => [
                        'time' => 318,
                    ],
                    'findDocSetsPerLeaf' => [
                        'time' => 0,
                    ],
                    'addIndexes' => [
                        'time' => 21,
                    ],
                    'subIWCommit' => [
                        'time' => 294,
                    ],
                ],
            ],
        ];

        $response = new Response(json_encode($data), ['HTTP/1.1 200 OK']);
        $result = new Result($query, $response);
        $parser = new ResponseParser();
        $parsed = $parser->parse($result);

        $this->assertSame($data['response'], $parsed['_original_response']);
        $this->assertArrayNotHasKey('response', $parsed);
    }
}
