<?php

namespace Solarium\Tests\QueryType\Spellcheck;

use PHPUnit\Framework\TestCase;
use Solarium\QueryType\Spellcheck\Query;
use Solarium\QueryType\Spellcheck\ResponseParser;
use Solarium\QueryType\Spellcheck\Result\Result;
use Solarium\QueryType\Spellcheck\Result\Term;

class ResponseParserTest extends TestCase
{
    public function testParse()
    {
        $data = [
            'responseHeader' => [
                'status' => 1,
                'QTime' => 13,
            ],
            'spellcheck' => [
                'suggestions' => [
                    'd',
                    [
                        'numFound' => 2,
                        'startOffset' => 3,
                        'endOffset' => 7,
                        'suggestion' => [
                            'disk',
                            'ddr',
                        ],
                    ],
                    'vid',
                    [
                        'numFound' => 1,
                        'startOffset' => 2,
                        'endOffset' => 5,
                        'suggestion' => [
                            'video',
                        ],
                    ],
                    'vid',
                    [
                        'numFound' => 1,
                        'startOffset' => 6,
                        'endOffset' => 9,
                        'suggestion' => [
                            'video',
                        ],
                    ],
                    'collation',
                    'disk video',
                ],
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
            'd' => new Term(2, 3, 7, ['disk', 'ddr']),
            'vid' => new Term(1, 2, 5, ['video']),
        ];
        $allExpected = [
            new Term(2, 3, 7, ['disk', 'ddr']),
            new Term(1, 2, 5, ['video']),
            new Term(1, 6, 9, ['video']),
        ];

        $this->assertEquals($expected, $result['results']);
        $this->assertEquals($allExpected, $result['all']);
        $this->assertSame('disk video', $result['collation']);
    }
}
