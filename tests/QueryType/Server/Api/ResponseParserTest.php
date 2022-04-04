<?php

namespace Solarium\Tests\QueryType\Server\Api;

use PHPUnit\Framework\TestCase;
use Solarium\QueryType\Server\Api\Query;
use Solarium\QueryType\Server\Api\ResponseParser;
use Solarium\QueryType\Server\Api\Result;

class ResponseParserTest extends TestCase
{
    public function testParse()
    {
        $data = [
            'responseHeader' => [
                'status' => 0,
                'QTime' => 13,
            ],
            'data' => [
                'foo' => 'bar',
            ],
        ];

        $query = new Query();

        $resultStub = $this->createMock(Result::class);
        $resultStub->expects($this->any())
             ->method('getData')
             ->willReturn($data);
        $resultStub->expects($this->any())
             ->method('getQuery')
             ->willReturn($query);

        $parser = new ResponseParser();
        $result = $parser->parse($resultStub);

        $expected = [
            'foo' => 'bar',
        ];

        $this->assertSame($expected, $result['data']);
    }
}
