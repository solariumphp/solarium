<?php

namespace Solarium\Tests\QueryType\Suggester;

use PHPUnit\Framework\TestCase;
use Solarium\QueryType\Suggester\Query;
use Solarium\QueryType\Suggester\ResponseParser;
use Solarium\QueryType\Suggester\Result\Dictionary;
use Solarium\QueryType\Suggester\Result\Result;
use Solarium\QueryType\Suggester\Result\Term;

class ResponseParserTest extends TestCase
{
    public function testParse()
    {
        $data = [
            'responseHeader' => [
                'status' => 1,
                'QTime' => 13,
            ],
            'suggest' => [
                'dictionary1' => [
                    'foo' => [
                        'numFound' => 2,
                        'suggestions' => [
                            [
                                'term' => 'foo',
                            ],
                            [
                                'term' => 'foobar',
                            ],
                        ],
                    ],
                    'zoo' => [
                        'numFound' => 1,
                        'suggestions' => [
                            [
                                'term' => 'zoo keeper',
                            ],
                        ],
                    ],
                ],
                'dictionary2' => [
                    'free' => [
                        'numFound' => 2,
                        'suggestions' => [
                            [
                                'term' => 'free beer',
                            ],
                            [
                                'term' => 'free software',
                            ],
                        ],
                    ],
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
            'dictionary1' => new Dictionary([
                'foo' => new Term(2, [['term' => 'foo'], ['term' => 'foobar']]),
                'zoo' => new Term(1, [['term' => 'zoo keeper']]),
            ]),
            'dictionary2' => new Dictionary([
                'free' => new Term(2, [['term' => 'free beer'], ['term' => 'free software']]),
            ]),
        ];
        $allExpected = [
            new Term(2, [['term' => 'foo'], ['term' => 'foobar']]),
            new Term(1, [['term' => 'zoo keeper']]),
            new Term(2, [['term' => 'free beer'], ['term' => 'free software']]),
        ];

        $this->assertEquals($expected, $result['results']);
        $this->assertEquals($allExpected, $result['all']);
    }
}
