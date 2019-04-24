<?php

namespace Solarium\Tests\QueryType\Terms;

use PHPUnit\Framework\TestCase;
use Solarium\QueryType\Terms\Query;
use Solarium\QueryType\Terms\ResponseParser;
use Solarium\QueryType\Terms\Result;

class ResponseParserTest extends TestCase
{
    public function testParse()
    {
        $data = [
            'responseHeader' => [
                'status' => 1,
                'QTime' => 13,
            ],
            'terms' => [
                'fieldA' => [
                    'term1',
                    5,
                    'term2',
                    3,
                ],
                'fieldB' => [
                    'term3',
                    6,
                    'term4',
                    2,
                ],
            ],
        ];

        $query = new Query();
        $query->setFields('fieldA,fieldB');

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
            'fieldA' => [
                'term1' => 5,
                'term2' => 3,
            ],
            'fieldB' => [
                'term3' => 6,
                'term4' => 2,
            ],
        ];

        $this->assertSame($expected, $result['results']);
        $this->assertCount(2, $result['results']);
    }
}
